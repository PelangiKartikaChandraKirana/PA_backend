@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Tambah Pengguna</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Daftarkan akun baru ke dalam ekosistem sistem SIAPMAN.</p>
        </div>
        <a href="{{ route('superadmin.pengguna.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Batal
        </a>
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

    <div class="max-w-5xl">
        <form action="{{ route('superadmin.pengguna.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Form Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                            <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Informasi Kredensial</h2>
                        </div>

                        <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Kom" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Username <span class="text-rose-500">*</span></label>
                                <input type="text" name="username" value="{{ old('username') }}" placeholder="budisantoso88" required autocomplete="off"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Email Aktif <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="budi@example.com" required autocomplete="off"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">NIP <span class="text-rose-500">*</span></label>
                                <input type="text" name="nip" value="{{ old('nip') }}" placeholder="19880123XXXXXXXX" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Role Akses <span class="text-rose-500">*</span></label>
                                <select name="role" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none cursor-pointer">
                                    <option value="">- Pilih Role -</option>
                                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Penempatan OPD <span class="text-rose-500">*</span></label>
                                <select name="company_id" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none cursor-pointer">
                                    <option value="">- Pilih OPD -</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Detail Unit Kerja <span class="text-rose-500">*</span></label>
                                <input type="text" name="unit_kerja" value="{{ old('unit_kerja') }}" placeholder="Contoh: Bidang Pelayanan Medis" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required placeholder="Minimal 8 karakter" autocomplete="new-password"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none tracking-widest">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Konfirmasi Pass <span class="text-rose-500">*</span></label>
                                <input type="password" name="password_confirmation" required placeholder="Ulangi password" autocomplete="new-password"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none tracking-widest">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                        <label class="flex cursor-pointer items-center gap-4">
                            <input type="hidden" name="status" value="Nonaktif">
                            <input type="checkbox" name="status" value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'checked' : '' }}
                                class="h-6 w-6 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500/20">
                            <div>
                                <p class="text-sm font-bold text-slate-800">Aktifkan Akun Segera</p>
                                <p class="text-xs font-medium text-slate-500 leading-tight">Jika dicentang, pengguna dapat langsung login setelah pendaftaran.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Right: Face Scan -->
                <div class="space-y-6">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg p-6 flex flex-col items-center">
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
                            <div id="camera-placeholder" class="h-full flex flex-col items-center justify-center p-8 text-center bg-slate-50">
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
                            class="w-full rounded-2xl bg-blue-600 py-4 text-sm font-black text-white shadow-xl shadow-blue-200 transition-all hover:bg-blue-700 active:scale-95 flex items-center justify-center gap-3 disabled:bg-slate-300 disabled:shadow-none"
                            disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        SIMPAN PENGGUNA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const FACE_SERVICE_URL = window.location.origin + "/face";

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
                    // Jika belum ada izin, label akan kosong. Kita beri nama generik.
                    option.text = device.label || `Kamera ${index + 1} (Beri Akses Dahulu)`;
                    if (currentDeviceId === device.deviceId) option.selected = true;
                    cameraSelect.appendChild(option);
                });

                // Selalu tampilkan dropdown agar opsi terlihat
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
                alert("ERR: Browser memblokir kamera karena URL tidak dianggap aman. \n\nSilakan gunakan http://127.0.0.1:8000 atau http://localhost:8000 sebagai gantinya.");
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
            if (status.includes("Idle") || status.includes("hilang")) msg = "WAJAH TIDAK TERDETEKSI";
            if (status.includes("Mohon diam") || status.includes("Stabilisasi")) msg = "DIAM (TAHAN WAJAH)";
            if (status.includes("berkedip")) msg = "SILAKAN BERKEDIP";
            if (status.includes("geleng")) msg = "GELENGKAN KEPALA (KIRI/KANAN)";
            if (status.includes("mulut")) msg = "BUKA MULUT ANDA";
            if (status.includes("HANYA BOLEH") || status.includes("satu wajah")) msg = "HANYA SATU WAJAH DIIZINKAN!";
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
            placeholder.classList.remove('hidden');
            instructionCard.classList.add('hidden');
            stepIndicator.classList.add('hidden'); // Hide sequence
            btnSubmit.disabled = true;
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
