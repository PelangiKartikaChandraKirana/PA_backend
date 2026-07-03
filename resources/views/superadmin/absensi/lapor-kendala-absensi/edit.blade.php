@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.index') }}"
           class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-400 hover:bg-gray-50 hover:text-blue-600 shadow-sm transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Kelola Tiket Kendala</h1>
            <p class="text-sm text-gray-500 mt-0.5">Perbarui status perbaikan atau detail laporan tiket <span class="font-bold text-indigo-600">#{{ $item->id }}</span>.</p>
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

    <form action="{{ route('superadmin.absensi.lapor-kendala-absensi.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Info Column --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-2">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Detail Laporan</h3>
                        <span class="text-[10px] font-mono font-bold text-gray-300">Dibuat: {{ $item->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Jenis Kendala <span class="text-red-500">*</span></label>
                            <select name="machine_fault_type_id" required
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 appearance-none bg-white">
                                @foreach($types as $t)
                                    <option value="{{ $t->id }}" @selected(old('machine_fault_type_id', $item->machine_fault_type_id) == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Tanggal Kejadian <span class="text-red-500">*</span></label>
                            <input type="date" name="incident_date" value="{{ old('incident_date', optional($item->incident_date)->format('Y-m-d')) }}" required
                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500">Deskripsi Masalah <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="6" required
                                  class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 resize-none font-medium text-gray-700">{{ old('description', $item->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Sidebar Status & Evidence Column --}}
            <div class="space-y-6">
                {{-- Status Control Card --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden p-6 border-l-4 border-l-blue-500">
                    <label class="mb-4 block text-xs font-bold uppercase tracking-wide text-gray-400">Status Penyelesaian <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        @foreach($statuses as $s)
                            @php
                                $sName = strtolower($s->name);
                                $colorClass = 'peer-checked:bg-gray-100 peer-checked:text-gray-900 border-gray-100';
                                if(str_contains($sName, 'pending') || str_contains($sName, 'baru')) $colorClass = 'peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:border-red-200';
                                elseif(str_contains($sName, 'proses')) $colorClass = 'peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:border-amber-200';
                                elseif(str_contains($sName, 'selesai') || str_contains($sName, 'resolved')) $colorClass = 'peer-checked:bg-green-50 peer-checked:text-green-700 peer-checked:border-green-200';
                            @endphp
                            <label class="relative flex items-center group cursor-pointer">
                                <input type="radio" name="machine_fault_status_id" value="{{ $s->id }}" 
                                       class="sr-only peer" @checked(old('machine_fault_status_id', $item->machine_fault_status_id) == $s->id)>
                                <div class="w-full rounded-xl border p-3 text-sm font-bold text-gray-500 transition-all hover:bg-gray-50 {{ $colorClass }}">
                                    <div class="flex items-center justify-between">
                                        <span>{{ $s->name }}</span>
                                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Evidence Selection Card --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden p-6">
                    <label class="mb-3 block text-xs font-bold uppercase tracking-wide text-gray-400">Bukti Visual</label>
                    
                    <div class="relative group h-48 w-full rounded-xl overflow-hidden bg-gray-100 border-2 border-dashed border-gray-200 hover:border-blue-300 transition-all">
                        <img id="image-preview" src="{{ $item->evidence_path ? asset('storage/'.$item->evidence_path) : '#' }}" 
                             alt="Bukti Tiket" 
                             class="{{ $item->evidence_path ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 group-hover:scale-110">
                        
                        <div id="upload-placeholder" class="{{ $item->evidence_path ? 'hidden' : '' }} flex flex-col items-center justify-center h-full text-center p-4">
                            <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Tidak ada bukti foto</p>
                        </div>

                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center z-20 transition-opacity">
                            <button type="button" onclick="document.getElementById('evidenceInput').click()" class="bg-white text-gray-900 rounded-lg px-4 py-2 text-xs font-bold shadow-xl active:scale-95 transition-all">Ganti Foto</button>
                        </div>
                        <input type="file" id="evidenceInput" name="evidence" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <p class="mt-2 text-[10px] text-gray-400 text-center font-medium">Klik gambar/area untuk mengganti bukti foto pendukung.</p>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-2 flex flex-col gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-4 text-sm font-bold text-white shadow-lg shadow-blue-100 transition-all hover:bg-blue-700 active:scale-95">
                        Simpan Perubahan Tiket
                    </button>
                    <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-600 shadow-sm transition-all hover:bg-gray-50 active:scale-95">
                        Batalkan
                    </a>
                </div>
            </div>
        </div>
    </form>

</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('upload-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
