@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.index') }}"
           class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-400 hover:bg-gray-50 hover:text-blue-600 shadow-sm transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Kirim Laporan Kendala</h1>
            <p class="text-sm text-gray-500 mt-0.5">Berikan detail kendala teknis yang dialami untuk segera ditindaklanjuti oleh tim IT.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700 shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="font-medium text-red-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden border-t-4 border-t-red-500">
        <form action="{{ route('superadmin.absensi.lapor-kendala-absensi.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kategori Kendala --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Jenis Kendala <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="machine_fault_type_id" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all appearance-none bg-white">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}" @selected(old('machine_fault_type_id') == $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Tanggal Kejadian --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Tanggal Kejadian <span class="text-red-500">*</span></label>
                    <input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                </div>
            </div>

            {{-- Deskripsi Masalah --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Deskripsi Kendala <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                          placeholder="Jelaskan secara detail masalah yang terjadi (contoh: Mesin Fingerprint di Lobby tidak menyala setelah mati lampu)..."
                          class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Bukti Kendala (Image) --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Unggah Bukti Foto</label>
                <div class="flex items-center justify-center w-full">
                    <label class="relative flex flex-col items-center justify-center w-full h-44 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-300 transition-all group overflow-hidden">
                        <div id="upload-placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="mb-1 text-sm text-gray-500 font-semibold uppercase tracking-tight">Klik untuk unggah gambar</p>
                            <p class="text-[10px] text-gray-400 uppercase">PNG atau JPG (Max. 4MB)</p>
                        </div>
                        <img id="image-preview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 hover:scale-105">
                        <input type="file" name="evidence" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <div id="remove-btn" class="hidden mt-2 flex justify-end">
                    <button type="button" onclick="resetImage()" class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Ganti Gambar
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-600 shadow-sm transition-all hover:bg-gray-50 active:scale-95">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('upload-placeholder');
                const removeBtn = document.getElementById('remove-btn');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                removeBtn.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetImage() {
        const input = document.querySelector('input[name="evidence"]');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const removeBtn = document.getElementById('remove-btn');
        
        input.value = '';
        preview.src = '#';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }
</script>
@endsection