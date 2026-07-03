@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-4xl">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.absensi.mesin.index') }}"
           class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-400 hover:bg-gray-50 hover:text-blue-600 shadow-sm transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Daftarkan Mesin Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">Input identitas fisik dan jaringan mesin untuk mulai melakukan sinkronisasi data.</p>
        </div>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Periksa kembali inputan Anda:
            </div>
            <ul class="list-disc list-inside space-y-1 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.absensi.mesin.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Identitas Fisik Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Identitas Fisik</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-600">Nama Mesin <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Fingerprint Lobby Utama"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-600">Serial Number (SN)</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                               placeholder="Contoh: ABC123XYZ"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all font-mono tracking-wider">
                        <p class="mt-1.5 text-[10px] text-gray-400 italic">Gunakan SN yang tertera pada bagian belakang perangkat atau kardus.</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-600">Unit Kerja / OPD</label>
                        <select name="unit_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 appearance-none bg-white">
                            <option value="">-- Lokasi Global (Non-OPD) --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" @selected(old('unit_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Konfigurasi Jaringan Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Jaringan & Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-600">IP Address</label>
                        <input type="text" name="ip_address" value="{{ old('ip_address') }}"
                               placeholder="Contoh: 192.168.1.100"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all font-mono">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-600">Lokasi Penempatan</label>
                        <input type="text" name="location_name" value="{{ old('location_name') }}"
                               placeholder="Contoh: Lantai 1, Dekat Tangga"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all">
                    </div>

                    <div class="pt-2">
                        <label class="mb-3 block text-xs font-bold uppercase tracking-wide text-gray-600">Status Monitoring</label>
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-bold text-gray-700 group-hover:text-blue-600 transition-colors">Aktifkan Monitoring</span>
                        </label>
                        <p class="mt-2 text-xs text-gray-400">Jika dinonaktifkan, mesin tidak akan muncul di dashboard health monitor.</p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('superadmin.absensi.mesin.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-600 shadow-sm transition-all hover:bg-gray-50 active:scale-95">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 active:scale-95">
                    Daftarkan Mesin Sekarang
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
