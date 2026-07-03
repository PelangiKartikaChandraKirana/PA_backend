@extends('layouts.app')

@section('content')
<div class="px-6 py-6 font-sans">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="group">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit Pengguna</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Kelola informasi kredensial, hak akses, dan biometrik untuk <b>{{ $user->name }}</b>.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 xl:flex flex-wrap items-center gap-2">
            <a href="{{ route('superadmin.pengguna.riwayat-login', $user->id) }}" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-black text-blue-700 tracking-widest uppercase transition-all hover:bg-blue-600 hover:text-white active:scale-95 shadow-sm shadow-blue-100/50">
                L-HIST
            </a>
            
            <a href="{{ route('superadmin.pengguna.perangkat', $user->id) }}" class="inline-flex items-center gap-2 rounded-xl border border-purple-200 bg-purple-50 px-4 py-2 text-xs font-black text-purple-700 tracking-widest uppercase transition-all hover:bg-purple-600 hover:text-white active:scale-95 shadow-sm shadow-purple-100/50">
                DEVICE
            </a>

            <a href="{{ route('superadmin.pengguna.lokasi-absen', $user->id) }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-black text-emerald-700 tracking-widest uppercase transition-all hover:bg-emerald-600 hover:text-white active:scale-95 shadow-sm shadow-emerald-100/50">
                LOC
            </a>

            <form action="{{ route('superadmin.pengguna.reset', $user->id) }}" method="POST" onsubmit="return confirm('Bagikan password standar \'password123\' untuk pengguna ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-black text-rose-700 tracking-widest uppercase transition-all hover:bg-rose-600 hover:text-white active:scale-95 shadow-sm shadow-rose-100/50">
                    RESET
                </button>
            </form>

            <a href="{{ route('superadmin.pengguna.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-black text-slate-700 tracking-widest uppercase transition-all hover:bg-slate-50 active:scale-95">
                ESC
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 animate-in fade-in slide-in-from-top-4">
            <div class="font-black uppercase tracking-widest text-[10px] mb-2 opacity-70">Terjadi Kesalahan Validasi</div>
            <ul class="list-disc pl-5 space-y-1 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-6xl">
        <form action="{{ route('superadmin.pengguna.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Form Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                            <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Account Credentials</h2>
                        </div>

                        <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">Username</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-400 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">Email Recovery</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">NIP</label>
                                <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">Role Authority</label>
                                <select name="role" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-black uppercase tracking-wider text-slate-600 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none cursor-pointer">
                                    <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>SUPERADMIN</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>ADMIN</option>
                                    <option value="pegawai" {{ old('role', $user->role) == 'pegawai' ? 'selected' : '' }}>PEGAWAI</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">Penempatan OPD <span class="text-rose-500">*</span></label>
                                <select name="department_id" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-black uppercase tracking-wider text-slate-600 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none cursor-pointer">
                                    <option value="">- Pilih OPD -</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1 tracking-tight">Detail Unit Kerja <span class="text-rose-500">*</span></label>
                                <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja) }}" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2 border-t border-slate-100 pt-4 md:col-span-2">
                                <div class="flex items-center justify-between mb-2 px-1">
                                    <label class="text-sm font-extrabold text-slate-800 tracking-tight">Ubah Kata Sandi</label>
                                    <span class="text-[10px] font-black text-slate-300 uppercase italic">Leave blank to keep current</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="password" name="password" placeholder="Password Baru" autocomplete="new-password"
                                        class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none tracking-widest">
                                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Ulang" autocomplete="new-password"
                                        class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none tracking-widest">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                        <label class="flex cursor-pointer items-center gap-4 group">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                class="h-6 w-6 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500/20 transition group-hover:scale-110">
                            <div>
                                <p class="text-sm font-black text-slate-800 tracking-tight">Akun Berstatus Aktif</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase leading-relaxed tracking-wider">Uncheck to revoke access immediately</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Right: Face Scan -->
                <div class="space-y-6">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg p-6 flex flex-col items-center">
                        @php
                            $activeFace = $user->employee ? $user->employee->activeFace : null;
                        @endphp
                        <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6 w-full text-center">Interactive Face Verification</h2>
                        
                        <!-- Step Indicator -->
                        <div id="step-indicator" class="flex items-center justify-center gap-4 mb-6 hidden">
                            <div id="step-1" class="h-8 w-8 rounded-full border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-300">1</div>
                            <div class="h-0.5 w-6 bg-slate-200" id="line-1"></div>
                            <div id="step-2" class="h-8 w-8 rounded-full border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-300">2</div>
                            <div class="h-0.5 w-6 bg-slate-200" id="line-2"></div>
                            <div id="step-3" class="h-8 w-8 rounded-full border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-300">3</div>
                        </div>

                        <!-- Instruction Alert -->
                        <div id="instruction-card" class="w-full p-4 rounded-2xl bg-blue-50 border border-blue-100 mb-6 hidden animate-in fade-in zoom-in">
                            <p id="instruction-text" class="text-xs font-black text-blue-700 text-center uppercase tracking-wider"></p>
                        </div>

                        <!-- Camera Selector (Moved outside) -->
                        <div id="camera-selector-wrapper" class="w-full mb-4">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Sumber Kamera:</label>
                            <select id="camera-select" class="w-full bg-slate-100 text-slate-800 text-xs px-4 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition appearance-none">
                                <option value="">Mendeteksi Kamera...</option>
                            </select>
                        </div>
                        
                        <div id="face-scan-container" class="relative group w-full aspect-square max-w-[280px] overflow-hidden rounded-full bg-slate-100 border-4 border-slate-50 shadow-inner ring-8 ring-slate-50 transition-all duration-500">
                            
                            <!-- Placeholder -->
                            
                            @if($activeFace)
                            <!-- Existing Face -->
                            <div id="existing-face-ui" class="h-full flex flex-col items-center justify-center relative">
                                <img src="{{ asset('storage/' . $activeFace->image_path) }}" class="h-full w-full object-cover grayscale-[30%]">
                                <div class="absolute inset-0 bg-indigo-900/10 mix-blend-multiply"></div>
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center">
                                    <button type="button" id="btn-re-scan" class="bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-[9px] font-black text-indigo-700 shadow-xl hover:bg-white transition active:scale-95 uppercase tracking-widest">PERBARUI FOTO</button>
                                </div>
                            </div>
                            @endif

                            <div id="camera-placeholder" class="{{ $activeFace ? 'hidden' : '' }} h-full flex flex-col items-center justify-center p-8 text-center bg-slate-50">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-md text-slate-800 mb-4 transition group-hover:scale-110">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3m0 18a10.003 10.003 0 01-8.212-4.33l-.054-.09m15.824 2.13a10.003 10.003 0 001.138-10.51M11.35 14.12L12 15l1.65-1.88M12 15V3"></path></svg>
                                </div>
                                <h3 class="text-sm font-black text-slate-800 tracking-tight">Kamera Diperlukan</h3>
                                <p class="text-[10px] font-medium text-slate-400 mt-1 leading-relaxed">Wajib verifikasi keaktifan wajah untuk pendaftaran.</p>
                                <button type="button" id="btn-start-camera" class="mt-6 w-full rounded-xl bg-slate-800 py-2.5 text-xs font-bold text-white shadow-xl shadow-slate-200 hover:bg-slate-900 transition active:scale-95">Mulai Verifikasi</button>
                            </div>

                            <!-- Browser Webcam Video (Hidden) -->
                            <video id="webcam-video" autoplay playsinline muted class="hidden"></video>
                            <canvas id="webcam-canvas" class="hidden"></canvas>

                            <!-- MJPEG Feed from Python Backend (Now used for annotated display) -->
                            <div id="camera-active" class="hidden h-full w-full relative">
                                <img id="stream-img" src="" class="h-full w-full object-cover scale-x-[-1] block">
                                <div class="absolute inset-0 border-[12px] border-slate-900/10 pointer-events-none rounded-full"></div>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-[85%] h-[85%] border-2 border-dashed border-white/50 rounded-full"></div>
                                </div>
                                <button type="button" id="btn-stop-camera" class="absolute top-4 right-4 text-white drop-shadow-md transition hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- Preview Successful Capture -->
                            <div id="camera-preview" class="hidden h-full relative">
                                <img id="face-preview-img" src="" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-emerald-500/10 mix-blend-overlay"></div>
                                <div class="absolute bottom-6 left-0 right-0 flex flex-col items-center gap-2">
                                    <span class="bg-emerald-500 text-white text-[9px] font-black uppercase px-3 py-1 rounded-full shadow-lg">VERIFIKASI BERHASIL</span>
                                    <button type="button" id="btn-retake" class="text-white drop-shadow-md text-[10px] font-black hover:underline tracking-widest">VERIFIKASI ULANG</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Panduan Asisten:</p>
                            <div class="flex items-start gap-2 text-[10px] text-slate-500 font-bold leading-tight">
                                <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                                <span>Pencahayaan cukup & hindari cahaya belakang.</span>
                            </div>
                            <div class="flex items-start gap-2 text-[10px] text-slate-500 font-bold leading-tight">
                                <i class="fas fa-user-slash text-blue-500 mt-0.5"></i>
                                <span>Lepas masker, kacamata, atau penutup wajah.</span>
                            </div>
                            <div class="flex items-start gap-2 text-[10px] text-slate-500 font-bold leading-tight">
                                <i class="fas fa-shield-alt text-emerald-500 mt-0.5"></i>
                                <span>Hanya satu wajah & gunakan wajah asli Anda.</span>
                            </div>
                        </div>
                        
                        <input type="hidden" name="face_image" id="face-image-input">
                    </div>

                    <button type="submit" id="btn-submit-form"
                            class="w-full rounded-2xl bg-blue-600 py-4 text-sm font-black text-white shadow-xl shadow-blue-200 transition-all hover:bg-blue-700 active:scale-95 flex items-center justify-center gap-3 disabled:bg-slate-300 disabled:shadow-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        UPDATE PENGGUNA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const FACE_SERVICE_URL = "http://127.0.0.1:5001";

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const faceImageInput = document.getElementById('face-image-input');
        const streamImg = document.getElementById('stream-img');
        const facePreviewImg = document.getElementById('face-preview-img');
        
        const placeholder = document.getElementById('camera-placeholder');
        const activeCamera = document.getElementById('camera-active');
        const previewUI = document.getElementById('camera-preview');
        const stepIndicator = document.getElementById('step-indicator');
        const instructionCard = document.getElementById('instruction-card');
        const instructionText = document.getElementById('instruction-text');
        const btnSubmit = document.getElementById('btn-submit-form');
        const existingFaceUI = document.getElementById('existing-face-ui');
        const btnReScan = document.getElementById('btn-re-scan');
        
        if(btnReScan) {
            btnReScan.addEventListener('click', () => {
                existingFaceUI.classList.add('hidden');
                placeholder.classList.remove('hidden');
            });
        }
        
        const btnStart = document.getElementById('btn-start-camera');
        const btnStop = document.getElementById('btn-stop-camera');
        const btnRetake = document.getElementById('btn-retake');
        
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const step3 = document.getElementById('step-3');
        const line1 = document.getElementById('line-1');
        const line2 = document.getElementById('line-2');

        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('webcam-canvas');
        const ctx = canvas.getContext('2d');

        let isRunning = false;
        let stream = null;
        let frameInterval = null;
        let currentDeviceId = null;
        const cameraSelect = document.getElementById('camera-select');

        async function getCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                
                cameraSelect.innerHTML = '';
                
                if (videoDevices.length === 0) {
                    const option = document.createElement('option');
                    option.text = "Kamera tidak terdeteksi";
                    cameraSelect.appendChild(option);
                    return;
                }

                videoDevices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.deviceId;
                    option.text = device.label || `Kamera ${index + 1} (Beri Akses Dahulu)`;
                    if (currentDeviceId === device.deviceId) option.selected = true;
                    cameraSelect.appendChild(option);
                });

                document.getElementById('camera-selector-wrapper').classList.remove('hidden');
                
            } catch (err) {
                console.error("Gagal melist kamera:", err);
            }
        }

        // Pancing browser untuk meminta izin & membaca kamera saat awal
        getCameras();

        async function startVerification() {
            if (isRunning) return;

            // Check if secure context (Required for camera)
            if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                alert(`ERR: Browser memblokir kamera karena URL tidak dianggap aman. \n\nSilakan gunakan http://127.0.0.1:8000 atau http://localhost:8000 sebagai gantinya.`);
                return;
            }

            try {
                await getCameras();
                
                const constraints = { 
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        deviceId: currentDeviceId ? { exact: currentDeviceId } : undefined
                    } 
                };

                // If no deviceId yet, default to user facing or the first one
                if (!currentDeviceId) {
                    constraints.video.facingMode = "user";
                }

                // Request Webcam
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;
                await video.play();
                
                // Reset backend state
                await fetch(`${FACE_SERVICE_URL}/reset`, { method: 'POST' });
                
                placeholder.classList.add('hidden');
                activeCamera.classList.remove('hidden');
                activeCamera.style.display = 'block'; // Force display
                instructionCard.classList.remove('hidden');
                stepIndicator.classList.remove('hidden'); // Show sequence
                
                isRunning = true;
                processFrame();
            } catch (err) {
                console.error(err);
                alert("ERR: Gagal mengakses webcam atau backend tidak aktif. Pastikan HTTPS aktif jika di remote.");
            }
        }

        async function processFrame() {
            if (!isRunning) return;

            // Draw current video frame to canvas
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Convert to Blob and send to backend
                canvas.toBlob(async (blob) => {
                    if (!blob) return;
                    
                    const formData = new FormData();
                    formData.append('face_image', blob, 'frame.jpg');
                    
                    try {
                        const response = await fetch(`${FACE_SERVICE_URL}/frame-upload`, {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        // Update UI with annotated image and status
                        if (data.image) streamImg.src = data.image;
                        updateUI(data);

                        if (data.valid) {
                            isRunning = false;
                            handleSuccess();
                        } else {
                            if (isRunning) frameInterval = setTimeout(processFrame, 100);
                        }
                    } catch (err) {
                        console.error("Frame processing failed", err);
                        if (isRunning) frameInterval = setTimeout(processFrame, 500);
                    }
                }, 'image/jpeg', 0.6);
            } else {
                if (isRunning) frameInterval = setTimeout(processFrame, 100);
            }
        }

        function updateUI(data) {
            const status = data.status;
            const warning = data.warning;

            // Update Instruction Text
            let msg = status;
            if (status.includes("Idle")) msg = "MOHON TUNGGU...";
            if (status.includes("Mohon diam") || status.includes("Stabilisasi")) msg = "DIAM (TAHAN WAJAH)";
            if (status.includes("berkedip")) msg = "SILAKAN BERKEDIP";
            if (status.includes("geleng")) msg = "GELENGKAN KEPALA (KIRI/KANAN)";
            if (status.includes("mulut")) msg = "BUKA MULUT ANDA";
            if (status.includes("HANYA BOLEH") || status.includes("satu wajah")) msg = "HANYA SATU WAJAH DIIZINKAN!";
            if (status.includes("hilang")) msg = "WAJAH TIDAK TERLIHAT!";
            if (data.valid) msg = "VERIFIKASI BERHASIL!";

            // Update Step Sequence Visuals
            const step = data.step !== undefined ? data.step : 0;
            const valid = data.valid;

            // Step 1: Stabilize
            if (step >= 0 || valid) {
                step1.className = "h-8 w-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-all duration-300 bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-200";
            } else {
                step1.className = "h-8 w-8 rounded-full border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-300";
            }

            // Step 2: Challenge 1
            if (step >= 1 || valid) {
                line1.className = "h-0.5 w-6 transition-all duration-300 bg-blue-600";
                step2.className = "h-8 w-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-all duration-300 bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-200";
            } else {
                line1.className = "h-0.5 w-6 bg-slate-200 transition-all duration-300";
                step2.className = "h-8 w-8 rounded-full border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-300";
            }

            // Step 3: Challenge 2
            if (step >= 2 || valid) {
                line2.className = "h-0.5 w-6 transition-all duration-300 bg-blue-600";
                step3.className = "h-8 w-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-all duration-300 bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-200";
            } else {
                line2.className = "h-0.5 w-6 bg-slate-200 transition-all duration-300";
                step3.className = "h-8 w-8 rounded-full border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-300";
            }

            if (warning && !data.valid) {
                instructionCard.classList.remove('bg-blue-50', 'border-blue-100');
                instructionCard.classList.add('bg-amber-50', 'border-amber-200');
                instructionText.classList.remove('text-blue-700');
                instructionText.classList.add('text-amber-700');
                instructionText.innerHTML = `<span class="block mb-1 text-[9px] opacity-70 underline uppercase">Peringatan:</span>${warning}<hr class="my-2 border-amber-200"><span class="block mt-1 text-blue-800">${msg}</span>`;
            } else {
                instructionCard.classList.add('bg-blue-50', 'border-blue-100');
                instructionCard.classList.remove('bg-amber-50', 'border-amber-200');
                instructionText.classList.add('text-blue-700');
                instructionText.classList.remove('text-amber-700');
                instructionText.innerText = msg;
            }
        }

        async function handleSuccess() {
            // Stop webcam stream
            if (stream) stream.getTracks().forEach(track => track.stop());
            
            try {
                // Ambil foto langsung dari canvas lokal browser (bersih tanpa kotak hijau)
                const localImage = canvas.toDataURL('image/jpeg', 0.8);
                
                if (localImage) {
                    faceImageInput.value = localImage;
                    facePreviewImg.src = localImage;
                    
                    activeCamera.classList.add('hidden');
                    previewUI.classList.remove('hidden');
                    instructionCard.classList.add('hidden');
                    stepIndicator.classList.add('hidden');
                    btnSubmit.disabled = false;
                }
            } catch (err) {
                console.error("Capture failed", err);
                alert("Gagal memproses foto lokal. Silakan coba lagi.");
            }
        }

        function stopVerification() {
            isRunning = false;
            if (frameInterval) clearTimeout(frameInterval);
            if (stream) stream.getTracks().forEach(track => track.stop());
            
            streamImg.src = "";
            activeCamera.classList.add('hidden');
            if(existingFaceUI) {
                existingFaceUI.classList.remove('hidden');
            } else {
                placeholder.classList.remove('hidden');
            }
            instructionCard.classList.add('hidden');
            stepIndicator.classList.add('hidden'); // Hide sequence
            btnSubmit.disabled = false;
        }

        btnStart.addEventListener('click', startVerification);
        btnStop.addEventListener('click', stopVerification);
        
        btnRetake.addEventListener('click', () => {
            previewUI.classList.add('hidden');
            startVerification();
        });

        cameraSelect.addEventListener('change', async (e) => {
            currentDeviceId = e.target.value;
            if (isRunning) {
                if (stream) stream.getTracks().forEach(track => track.stop());
                isRunning = false;
                if (frameInterval) clearTimeout(frameInterval);
                startVerification();
            }
        });

        form.addEventListener('submit', function(e) {
            if (!faceImageInput.value) {
                e.preventDefault();
                alert('Pendaftaran wajah wajib dilakukan!');
            }
        });
    });
</script>
@endsection