from flask import Flask, Response, jsonify, request
from flask_cors import CORS
import cv2
import time
import threading
import face_recognition
import os
import pickle
import numpy as np
import random
import secrets
import base64

try:
    from cryptography.fernet import Fernet, InvalidToken
except Exception:
    Fernet = None
    InvalidToken = Exception

app = Flask(__name__)
CORS(app) # Enable CORS for all routes

# ================= SETTINGS =================
KNOWN_FACES_DIR = os.path.join(os.path.dirname(__file__), "known_faces")
if not os.path.exists(KNOWN_FACES_DIR):
    os.makedirs(KNOWN_FACES_DIR)

STABILIZE_SECONDS = 1.0
CAMERA_INDEX = 0
GRACE_PERIOD_SECONDS = 3.0 
LIVENESS_PROOF_TTL_SECONDS = 30.0
MIN_FRAME_INTERVAL_SECONDS = 0.20
CHALLENGE_ACTIONS = ("blink", "turn")
_is_prod = os.getenv("APP_ENV", "development").lower() == "production"
ALLOW_SERVER_CAMERA = os.getenv(
    "FACE_ALLOW_SERVER_CAMERA",
    "false" if _is_prod else "true"
).lower() in {"1", "true", "yes", "on"}
FACE_MATCH_THRESHOLD = float(os.getenv("FACE_MATCH_THRESHOLD", "0.6"))
EMBEDDINGS_KEY = os.getenv("FACE_EMBEDDINGS_KEY", "").strip()

# Thresholds for liveliness (Relaxed for better UX)
EAR_THRESHOLD = 0.24
TURN_THRESHOLD = 1.15
MOUTH_OPEN_THRESHOLD = 0.25
PASSIVE_BLUR_MIN = float(os.getenv("FACE_PASSIVE_BLUR_MIN", "15.0"))
PASSIVE_HIGHLIGHT_MAX = float(os.getenv("FACE_PASSIVE_HIGHLIGHT_MAX", "0.30"))

# ================= GLOBAL STATE =================
_state_lock = threading.Lock()
_camera = None
_cascade = None
_first_detected_at = None
_status_text = "Idle"
_valid = False
_elapsed_sec = 0.0
_last_face_seen_at = 0.0

# Liveliness State Machine
# Steps: 0: Stabilize, 1..N: Randomized challenge actions, N+1: Validated
_current_step = 0
_step_start_time = 0.0
_blink_detected = False
_turn_detected = False
_blink_count = 0
_turn_left = False
_turn_right = False
_challenge_sequence = []
_session_id = None
_proof_token = None
_proof_expires_at = 0.0
_last_frame_processed_at = 0.0
_mouth_detected = False
_motion_samples = []
_last_face_center = None
_warning_text = ""

_known_faces = {}

_fernet = None
if EMBEDDINGS_KEY:
    if Fernet is None:
        print("WARNING: FACE_EMBEDDINGS_KEY di-set tetapi modul cryptography tidak tersedia. Penyimpanan wajah TIDAK terenkripsi.")
    else:
        try:
            _fernet = Fernet(EMBEDDINGS_KEY.encode("utf-8"))
            print("INFO: Enkripsi embedding wajah aktif.")
        except Exception as ex:
            print(f"WARNING: FACE_EMBEDDINGS_KEY tidak valid ({ex}). Penyimpanan wajah TIDAK terenkripsi.")

def _secure_write_bytes(path: str, payload: bytes):
    fd = os.open(path, os.O_WRONLY | os.O_CREAT | os.O_TRUNC, 0o600)
    with os.fdopen(fd, "wb") as f:
        f.write(payload)

def _encrypt_bytes(payload: bytes) -> bytes:
    if _fernet is None:
        return payload
    return _fernet.encrypt(payload)

def _decrypt_bytes(payload: bytes) -> bytes:
    if _fernet is None:
        return payload
    try:
        return _fernet.decrypt(payload)
    except InvalidToken:
        # Backward compatible: allow old plaintext embeddings to be loaded.
        return payload

# ================= LOAD FACES =================
def load_known_faces():
    global _known_faces
    _known_faces = {}
    for filename in os.listdir(KNOWN_FACES_DIR):
        if filename.endswith(".pkl"):
            user_id = filename.replace(".pkl", "")
            try:
                with open(os.path.join(KNOWN_FACES_DIR, filename), "rb") as f:
                    blob = f.read()
                decoded = _decrypt_bytes(blob)
                _known_faces[user_id] = pickle.loads(decoded)
            except Exception as e:
                print(f"Error loading {filename}: {e}")

load_known_faces()

# ================= CAMERA =================
def _get_camera():
    global _camera
    if _camera is None or not _camera.isOpened():
        _camera = cv2.VideoCapture(CAMERA_INDEX)
    return _camera

def _get_cascade():
    global _cascade
    if _cascade is None:
        _cascade = cv2.CascadeClassifier(
            cv2.data.haarcascades + "haarcascade_frontalface_default.xml"
        )
    return _cascade

# ================= MATH HELPERS =================
def _calculate_ear(eye):
    # eye is list of (x,y)
    p2_p6 = np.linalg.norm(np.array(eye[1]) - np.array(eye[5]))
    p3_p5 = np.linalg.norm(np.array(eye[2]) - np.array(eye[4]))
    p1_p4 = np.linalg.norm(np.array(eye[0]) - np.array(eye[3]))
    if p1_p4 == 0: return 0
    return (p2_p6 + p3_p5) / (2.0 * p1_p4)

def _calculate_mar(top_lip, bottom_lip):
    if not top_lip or not bottom_lip:
        return 0.0
    top_center = np.mean(np.array(top_lip), axis=0)
    bottom_center = np.mean(np.array(bottom_lip), axis=0)
    left_corner = np.array(top_lip[0])
    right_corner = np.array(top_lip[-1])
    width = np.linalg.norm(right_corner - left_corner)
    if width <= 0:
        return 0.0
    opening = np.linalg.norm(bottom_center - top_center)
    return float(opening / width)

def _passive_liveness_ok(frame, face_bbox):
    if frame is None or frame.size == 0 or face_bbox is None:
        return False, "Frame kamera tidak valid"

    x, y, w, h = face_bbox
    x = max(0, int(x))
    y = max(0, int(y))
    w = max(1, int(w))
    h = max(1, int(h))

    roi = frame[y:y+h, x:x+w]
    if roi.size == 0:
        return False, "ROI wajah kosong"

    gray = cv2.cvtColor(roi, cv2.COLOR_BGR2GRAY)
    blur_score = cv2.Laplacian(gray, cv2.CV_64F).var()
    if blur_score < PASSIVE_BLUR_MIN:
        return False, "Wajah terlalu blur (indikasi spoof/foto)"

    highlight_ratio = float(np.mean(gray > 245))
    if highlight_ratio > PASSIVE_HIGHLIGHT_MAX:
        return False, "Pantulan berlebih terdeteksi (indikasi layar)"

    return True, ""

def _get_warning_and_instruction():
    global _status_text, _warning_text, _current_step, _valid, _challenge_sequence
    
    warning = _warning_text
    instruction = _status_text
    
    if _current_step > 0 and not _valid:
        idx = _current_step - 1
        if 0 <= idx < len(_challenge_sequence):
            instruction = _action_prompt(_challenge_sequence[idx])
            
    return warning, instruction

_last_valid_frame = None

def _action_label(action: str) -> str:
    if action == "blink":
        return "Berkedip"
    if action == "turn":
        return "Gelengkan kepala"
    if action == "mouth":
        return "Buka mulut"
    return "Aksi tidak dikenal"

def _action_prompt(action: str) -> str:
    if action == "blink":
        return "Silakan berkedip"
    if action == "turn":
        return "Silakan gelengkan kepala Anda"
    if action == "mouth":
        return "Silakan buka mulut sebentar"
    return "Lakukan instruksi di layar"

def _new_challenge_sequence():
    sequence = random.sample(list(CHALLENGE_ACTIONS), k=2)
    return sequence

def _issue_new_session_locked():
    global _session_id, _challenge_sequence
    _session_id = secrets.token_urlsafe(12)
    _challenge_sequence = _new_challenge_sequence()

def _status_payload_locked():
    now = time.time()
    proof_ttl = max(0.0, _proof_expires_at - now) if _proof_token else 0.0
    warning, instruction = _get_warning_and_instruction()

    return {
        "valid": _valid,
        "status": instruction,
        "warning": warning,
        "step": _current_step,
        "elapsed": round(_elapsed_sec, 2),
        "required": STABILIZE_SECONDS if _current_step == 0 else 1.0,
        "session_id": _session_id,
        "challenge_total_steps": len(_challenge_sequence),
        "proof_ttl_seconds": round(proof_ttl, 2),
        "liveness_token": _proof_token if _valid and _proof_token else None
    }

# ================= VALIDATION =================
def _reset_validation_locked():
    global _first_detected_at, _status_text, _valid, _elapsed_sec, _last_face_seen_at, _current_step, _blink_detected, _turn_detected, _step_start_time, _last_valid_frame, _proof_token, _proof_expires_at, _last_frame_processed_at, _mouth_detected, _motion_samples, _last_face_center, _blink_count, _turn_left, _turn_right
    _first_detected_at = None
    _status_text = "Idle"
    _warning_text = ""
    _valid = False
    _elapsed_sec = 0.0
    _last_face_seen_at = 0.0
    _current_step = 0
    _step_start_time = 0.0
    _blink_detected = False
    _turn_detected = False
    _blink_count = 0
    _turn_left = False
    _turn_right = False
    _mouth_detected = False
    _last_valid_frame = None
    _proof_token = None
    _proof_expires_at = 0.0
    _last_frame_processed_at = 0.0
    _motion_samples = []
    _last_face_center = None
    _issue_new_session_locked()

def _complete_current_step_locked(frame, now: float):
    global _current_step, _step_start_time, _blink_detected, _turn_detected, _mouth_detected, _valid, _status_text, _last_valid_frame, _proof_token, _proof_expires_at, _blink_count, _turn_left, _turn_right
    _current_step += 1
    _step_start_time = now
    _blink_detected = False
    _turn_detected = False
    _blink_count = 0
    _turn_left = False
    _turn_right = False
    _mouth_detected = False

    if (_current_step - 1) >= len(_challenge_sequence):
        _valid = True
        _status_text = "Liveness valid. Lanjutkan verifikasi absensi."
        _last_valid_frame = frame.copy()
        _proof_token = secrets.token_urlsafe(24)
        _proof_expires_at = now + LIVENESS_PROOF_TTL_SECONDS
        print(">>> SUCCESS: All challenge steps completed! <<<")
    else:
        next_action = _challenge_sequence[_current_step - 1]
        _status_text = _action_prompt(next_action)
        print(f">>> STEP {_current_step}: {_action_label(next_action)} <<<")

def _update_validation_locked(frame, faces, now: float):
    global _first_detected_at, _status_text, _valid, _elapsed_sec, _last_face_seen_at, _current_step, _blink_detected, _turn_detected, _step_start_time, _last_valid_frame, _proof_token, _proof_expires_at, _last_frame_processed_at, _mouth_detected, _motion_samples, _last_face_center, _blink_count, _turn_left, _turn_right

    if (_last_frame_processed_at > 0) and ((now - _last_frame_processed_at) < MIN_FRAME_INTERVAL_SECONDS):
        return
    _last_frame_processed_at = now

    if faces is None or len(faces) == 0:
        if _last_face_seen_at > 0 and (now - _last_face_seen_at) > GRACE_PERIOD_SECONDS:
            print(f">>> RESET: Wajah hilang selama {now - _last_face_seen_at:.1f} detik (Melebihi toleransi) <<<")
            _reset_validation_locked()
        return

    if len(faces) > 1:
        print(f">>> RESET: Terdeteksi {len(faces)} wajah. Hanya boleh satu wajah! <<<")
        _status_text = "HANYA BOLEH SATU WAJAH! (Resetting...)"
        _reset_validation_locked()
        return

    face_bbox = faces[0]
    passive_ok, passive_msg = _passive_liveness_ok(frame, face_bbox)
    if not passive_ok:
        _warning_text = passive_msg
    else:
        _warning_text = ""

    _last_face_seen_at = now

    if _valid:
        if now > _proof_expires_at:
            _status_text = "Token liveness kedaluwarsa. Silakan ulangi."
            _reset_validation_locked()
            return
        _status_text = "Wajah Valid"
        return

    x, y, w, h = face_bbox
    current_center = (x + (w / 2.0), y + (h / 2.0))
    if _last_face_center is not None:
        dx = current_center[0] - _last_face_center[0]
        dy = current_center[1] - _last_face_center[1]
        motion = float(np.sqrt(dx * dx + dy * dy))
        _motion_samples.append(motion)
        if len(_motion_samples) > 20:
            _motion_samples = _motion_samples[-20:]
    _last_face_center = current_center

    if _first_detected_at is None:
        _first_detected_at = now
        _step_start_time = now
        print(">>> START: Step 0 (Stabilizing) <<<")

    rgb_frame = np.ascontiguousarray(frame[:, :, ::-1])
    css_face_bbox = [(y, x + w, y + h, x)]
    landmarks_list = face_recognition.face_landmarks(rgb_frame, face_locations=css_face_bbox)

    if not landmarks_list:
        _status_text = "Look straight"
        return

    landmarks = landmarks_list[0]

    if _current_step == 0:
        _elapsed_sec = now - _step_start_time
        _status_text = "Mohon diam... Stabilisasi wajah"

        if _elapsed_sec >= STABILIZE_SECONDS:
            avg_motion = float(np.mean(_motion_samples)) if _motion_samples else 0.0
            if avg_motion < 0.20:
                _status_text = "Gerakkan kepala sedikit agar sistem memastikan ini wajah asli."
                _step_start_time = now
                _motion_samples = []
                return

            _current_step = 1
            _step_start_time = now
            _blink_detected = False
            _turn_detected = False
            _blink_count = 0
            _turn_left = False
            _turn_right = False
            _mouth_detected = False
            first_action = _challenge_sequence[0]
            _status_text = _action_prompt(first_action)
            print(f">>> STEP 1: {_action_label(first_action)} <<<")
        return

    action_idx = _current_step - 1
    if action_idx < 0 or action_idx >= len(_challenge_sequence):
        _status_text = "Status challenge tidak valid. Mereset sesi..."
        _reset_validation_locked()
        return

    current_action = _challenge_sequence[action_idx]
    _status_text = _action_prompt(current_action)

    if current_action == "blink":
        left_eye = landmarks.get('left_eye')
        right_eye = landmarks.get('right_eye')
        if left_eye and right_eye:
            ear_l = _calculate_ear(left_eye)
            ear_r = _calculate_ear(right_eye)
            avg_ear = (ear_l + ear_r) / 2.0

            if avg_ear < EAR_THRESHOLD:
                _blink_detected = True

            if _blink_detected and avg_ear > (EAR_THRESHOLD + 0.04) and (now - _step_start_time) > 0.3:
                _complete_current_step_locked(frame, now)
        return

    if current_action == "turn":
        nose = landmarks.get('nose_bridge')
        left_eye = landmarks.get('left_eye')
        right_eye = landmarks.get('right_eye')

        if nose and left_eye and right_eye:
            nose_tip = landmarks.get('nose_tip', nose)[-1]
            eye_l = left_eye[0]
            eye_r = right_eye[3]

            dist_l = np.linalg.norm(np.array(nose_tip) - np.array(eye_l))
            dist_r = np.linalg.norm(np.array(nose_tip) - np.array(eye_r))
            
            # Kiri: dist_r > dist_l * TURN_THRESHOLD
            if dist_r > dist_l * TURN_THRESHOLD:
                _turn_left = True
            # Kanan: dist_l > dist_r * TURN_THRESHOLD
            if dist_l > dist_r * TURN_THRESHOLD:
                _turn_right = True

            msg = "Silakan gelengkan kepala (Kanan & Kiri)"
            if _turn_left and not _turn_right:
                msg = "Bagus, sekarang balas geleng ke Kanan"
            elif _turn_right and not _turn_left:
                msg = "Bagus, sekarang balas geleng ke Kiri"
            
            _status_text = msg
            
            if _turn_left and _turn_right:
                ratio = max(dist_l, dist_r) / (min(dist_l, dist_r) + 0.001)
                if ratio < 1.15: # Kembali ke tengah
                    _complete_current_step_locked(frame, now)
        return

    if current_action == "mouth":
        top_lip = landmarks.get("top_lip")
        bottom_lip = landmarks.get("bottom_lip")
        mar = _calculate_mar(top_lip, bottom_lip)
        if mar > MOUTH_OPEN_THRESHOLD:
            _mouth_detected = True

        if _mouth_detected and mar < (MOUTH_OPEN_THRESHOLD - 0.08) and (now - _step_start_time) > 0.8:
            _complete_current_step_locked(frame, now)
        return

    _status_text = "Aksi challenge tidak didukung. Mereset sesi..."
    _reset_validation_locked()
    return

with _state_lock:
    _reset_validation_locked()

# ================= ROUTES =================
@app.route('/')
def home():
    return jsonify({
        "message": "Face service aktif",
        "registered_users": list(_known_faces.keys()),
        "allow_server_camera": ALLOW_SERVER_CAMERA,
        "match_threshold": FACE_MATCH_THRESHOLD,
        "embedding_encryption": _fernet is not None,
    })

@app.route('/health')
def health():
    return jsonify({
        "status": "ok",
        "time": int(time.time()),
        "allow_server_camera": ALLOW_SERVER_CAMERA,
        "registered_users": len(_known_faces),
    })

@app.route('/register', methods=['POST'])
def register():
    try:
        file = request.files['face_image']
        user_id = request.form.get('user_id')
        if not user_id:
            return jsonify({"message": "user_id wajib diisi"}), 422

        image = face_recognition.load_image_file(file)
        face_locations = face_recognition.face_locations(image)
        
        if not face_locations:
            return jsonify({"message": "Wajah Tidak Terdeteksi"}), 422
            
        # Ambil wajah yang paling besar (paling depan)
        largest_face = max(face_locations, key=lambda rect: abs((rect[2]-rect[0]) * (rect[1]-rect[3])))
        
        encodings = face_recognition.face_encodings(image, known_face_locations=[largest_face])
        if not encodings:
            return jsonify({"message": "Gagal mengekstrak fitur wajah"}), 422

        encoding = encodings[0]

        payload = pickle.dumps(encoding, protocol=pickle.HIGHEST_PROTOCOL)
        encrypted = _encrypt_bytes(payload)
        _secure_write_bytes(os.path.join(KNOWN_FACES_DIR, f"{user_id}.pkl"), encrypted)

        _known_faces[user_id] = encoding

        return jsonify({"message": "wajah berhasil didaftarkan"})

    except Exception as e:
        return jsonify({"message": str(e)}), 500

@app.route('/verify', methods=['POST'])
def verify():
    global _proof_token, _proof_expires_at
    try:
        file = request.files['face_image']
        user_id = request.form.get('user_id')
        if not user_id:
            return jsonify({"matched": False, "message": "user_id wajib diisi"}), 422
        session_id = request.form.get('session_id')
        liveness_token = request.form.get('liveness_token')

        now = time.time()
        with _state_lock:
            if not _valid:
                return jsonify({
                    "matched": False,
                    "message": "Liveness belum valid. Selesaikan challenge terlebih dahulu."
                }), 403

            if not session_id or session_id != _session_id:
                return jsonify({
                    "matched": False,
                    "message": "Sesi challenge tidak valid atau sudah berubah."
                }), 403

            if not liveness_token or liveness_token != _proof_token:
                return jsonify({
                    "matched": False,
                    "message": "Token liveness tidak valid."
                }), 403

            if now > _proof_expires_at:
                _reset_validation_locked()
                return jsonify({
                    "matched": False,
                    "message": "Token liveness sudah kedaluwarsa. Ulangi challenge."
                }), 403

            # One-time proof token. Prevent replay after one verification attempt.
            _proof_token = None
            _proof_expires_at = 0.0

        from PIL import Image, ImageOps
        import io

        # Gunakan PIL untuk menangani rotasi EXIF otomatis dari HP
        pil_img = Image.open(file)
        pil_img = ImageOps.exif_transpose(pil_img) # Koreksi rotasi otomatis
        
        # Konversi ke RGB (face_recognition butuh RGB)
        if pil_img.mode != 'RGB':
            pil_img = pil_img.convert('RGB')
            
        image = np.array(pil_img)
        
        # Coba deteksi normal
        face_locations = face_recognition.face_locations(image, number_of_times_to_upsample=1)
        
        # Jika gagal, coba perkecil gambar (terkadang resolusi terlalu tinggi bikin AI bingung)
        if not face_locations:
            h, w = image.shape[:2]
            if w > 1000:
                small_img = cv2.resize(image, (0,0), fx=0.5, fy=0.5)
                face_locations = face_recognition.face_locations(small_img, number_of_times_to_upsample=1)
                if face_locations:
                    image = small_img # Gunakan gambar yang lebih kecil jika berhasil

        unknown_encodings = face_recognition.face_encodings(image, known_face_locations=face_locations, num_jitters=1)
        
        if not unknown_encodings:
             with _state_lock:
                 _reset_validation_locked()
             return jsonify({
                 "matched": False, 
                 "message": "Wajah tidak terdeteksi pada foto. Pastikan pencahayaan cukup dan wajah menghadap lurus ke kamera."
             }), 422
        if len(unknown_encodings) > 1:
            with _state_lock:
                _reset_validation_locked()
            return jsonify({"matched": False, "message": "Terdeteksi lebih dari satu wajah pada foto"}), 422
             
        unknown_encoding = unknown_encodings[0]
        # Pastikan user_id berupa string
        uid = str(user_id)
        known_encoding = _known_faces.get(uid)
        
        if known_encoding is None:
            with _state_lock:
                _reset_validation_locked()
            return jsonify({"matched": False, "message": f"Wajah untuk user {uid} belum terdaftar"}), 422

        distance = face_recognition.face_distance([known_encoding], unknown_encoding)[0]
        matched = bool(distance < FACE_MATCH_THRESHOLD)

        with _state_lock:
            _reset_validation_locked()

        return jsonify({
            "matched": matched,
            "confidence": max(0.0, min(1.0, 1.0 - float(distance))),
            "distance": float(distance),
            "threshold": FACE_MATCH_THRESHOLD
        })

    except Exception as e:
        return jsonify({"message": str(e)}), 500

@app.route('/verify_face_only', methods=['POST'])
def verify_face_only():
    try:
        file = request.files['face_image']
        user_id = request.form.get('user_id')
        if not user_id:
            return jsonify({"matched": False, "message": "user_id wajib diisi"}), 422

        from PIL import Image, ImageOps

        # Gunakan PIL untuk menangani rotasi EXIF otomatis dari HP
        pil_img = Image.open(file)
        pil_img = ImageOps.exif_transpose(pil_img) # Koreksi rotasi otomatis
        
        # Konversi ke RGB (face_recognition butuh RGB)
        if pil_img.mode != 'RGB':
            pil_img = pil_img.convert('RGB')
            
        image = np.array(pil_img)
        
        # Resize gambar di awal untuk menghemat RAM dan CPU (Max dimension 400px)
        h, w = image.shape[:2]
        max_dim = max(h, w)
        if max_dim > 400:
            scale = 400.0 / float(max_dim)
            image = cv2.resize(image, (int(w * scale), int(h * scale)))
        # Deteksi wajah (upsample=0 untuk menghemat CPU dan RAM secara drastis)
        face_locations = face_recognition.face_locations(image, number_of_times_to_upsample=0)

        unknown_encodings = face_recognition.face_encodings(image, known_face_locations=face_locations, num_jitters=1)
        
        if not unknown_encodings:
             return jsonify({
                 "matched": False, 
                 "message": "Wajah tidak terdeteksi pada foto. Pastikan pencahayaan cukup dan wajah menghadap lurus ke kamera."
             }), 422
        if len(unknown_encodings) > 1:
            return jsonify({"matched": False, "message": "Terdeteksi lebih dari satu wajah pada foto"}), 422
             
        unknown_encoding = unknown_encodings[0]
        # Pastikan user_id berupa string
        uid = str(user_id)
        known_encoding = _known_faces.get(uid)
        
        if known_encoding is None:
            return jsonify({"matched": False, "message": f"Wajah untuk user {uid} belum terdaftar"}), 422

        distance = face_recognition.face_distance([known_encoding], unknown_encoding)[0]
        matched = bool(distance < FACE_MATCH_THRESHOLD)

        return jsonify({
            "matched": matched,
            "confidence": max(0.0, min(1.0, 1.0 - float(distance))),
            "distance": float(distance),
            "threshold": FACE_MATCH_THRESHOLD
        })

    except Exception as e:
        return jsonify({"message": str(e)}), 500

@app.route('/status')
def status():
    with _state_lock:
        return jsonify(_status_payload_locked())

@app.route('/reset', methods=['POST'])
def reset():
    with _state_lock:
        _reset_validation_locked()
        return jsonify({
            "message": "reset ok",
            "session_id": _session_id,
            "challenge_sequence": [_action_label(a) for a in _challenge_sequence]
        })

def _extract_best_face(frame):
    small_frame = cv2.resize(frame, (0, 0), fx=0.5, fy=0.5)
    rgb_small_frame = np.ascontiguousarray(small_frame[:, :, ::-1])
    face_locations = face_recognition.face_locations(rgb_small_frame)
    if not face_locations:
        return []

    landmarks_list = face_recognition.face_landmarks(rgb_small_frame, face_locations=face_locations)
    best_face = None
    max_area = 0
    frame_h, frame_w = frame.shape[:2]
    center_x, center_y = frame_w // 2, frame_h // 2

    for i, (top, right, bottom, left) in enumerate(face_locations):
        lm = landmarks_list[i] if i < len(landmarks_list) else None
        if not lm or 'left_eye' not in lm or 'nose_bridge' not in lm:
            continue

        x, y = left * 2, top * 2
        w, h = (right - left) * 2, (bottom - top) * 2
        area = w * h
        face_center_x = x + (w // 2)
        face_center_y = y + (h // 2)
        dist_from_center = np.sqrt((face_center_x - center_x) ** 2 + (face_center_y - center_y) ** 2)

        if w > 80 and dist_from_center < (frame_w * 0.45):
            if area > max_area:
                max_area = area
                best_face = (x, y, w, h)

    return [best_face] if best_face else []

def _apply_liveness_and_get_payload(frame):
    now = time.time()
    faces_list = _extract_best_face(frame)
    with _state_lock:
        _update_validation_locked(frame, faces_list, now)
        payload = _status_payload_locked()
    return payload, faces_list

@app.route('/frame')
def get_frame():
    if not ALLOW_SERVER_CAMERA:
        return jsonify({"message": "Endpoint frame kamera server dinonaktifkan"}), 403

    cap = _get_camera()
    ok, frame = cap.read()
    if not ok or frame is None or frame.size == 0:
        return jsonify({"message": "Gagal mengambil frame dari kamera"}), 500

    return _process_frame_route(frame)

@app.route('/frame-upload', methods=['POST'])
def frame_upload():
    if 'face_image' not in request.files:
        return jsonify({"message": "face_image wajib diisi"}), 422

    file = request.files['face_image']
    file_bytes = file.read()
    if not file_bytes:
        return jsonify({"message": "File gambar kosong"}), 422

    np_bytes = np.frombuffer(file_bytes, np.uint8)
    frame = cv2.imdecode(np_bytes, cv2.IMREAD_COLOR)
    if frame is None or frame.size == 0:
        return jsonify({"message": "Gagal membaca gambar"}), 422

    return _process_frame_route(frame)

def _process_frame_route(frame):
    payload, faces_list = _apply_liveness_and_get_payload(frame)
    frame = _annotate_frame(frame, faces_list)
    _, buffer = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 60])
    jpg_as_text = base64.b64encode(buffer).decode('utf-8')
    return jsonify({
        "image": f"data:image/jpeg;base64,{jpg_as_text}",
        **payload,
    })

@app.route('/capture')
def capture():
    with _state_lock:
        if _last_valid_frame is None:
            return jsonify({"message": "Tidak ada frame valid"}), 404
        
        _, buffer = cv2.imencode('.jpg', _last_valid_frame)
        jpg_as_text = base64.b64encode(buffer).decode('utf-8')
        return jsonify({"image": f"data:image/jpeg;base64,{jpg_as_text}"})

# ================= STREAM =================
def _annotate_frame(frame, faces):
    for (x, y, w, h) in faces:
        cv2.rectangle(frame, (x, y), (x+w, y+h), (0,255,0), 2)
    return frame

def _stream_frames():
    cap = _get_camera()

    while True:
        ok, frame = cap.read()
        if not ok:
            print("ERROR: Kamera tidak bisa dibuka!")
            time.sleep(1.0)
            continue

        faces_list = _extract_best_face(frame)
        with _state_lock:
            _update_validation_locked(frame, faces_list, time.time())

        frame = _annotate_frame(frame, faces_list)
        _, jpeg = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 70])
        yield (b'--frame\r\nContent-Type: image/jpeg\r\n\r\n' + jpeg.tobytes() + b'\r\n')

@app.route('/stream')
def stream():
    if not ALLOW_SERVER_CAMERA:
        return jsonify({"message": "Endpoint stream kamera server dinonaktifkan"}), 403

    return Response(_stream_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

# ================= MAIN =================
if __name__ == '__main__':
    with _state_lock:
        _reset_validation_locked()
    # Gunakan threaded=True agar streaming tidak memblokir route status
    app.run(host='0.0.0.0', port=5001, threaded=True)