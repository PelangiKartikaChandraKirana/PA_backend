@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.absensi.perangkat-pengguna.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Perangkat</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tautkan akun user ke ID perangkat (IMEI/UUID) tertentu untuk keamanan.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
            <p class="font-semibold mb-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Terdapat kesalahan input:
            </p>
            <ul class="list-disc list-inside space-y-0.5 ml-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.absensi.perangkat-pengguna.store') }}">
        @csrf
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-200">
            
            <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-tight">Konfigurasi Perangkat</h2>
            </div>

            <div class="p-6 space-y-5">
                {{-- User Selection --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700">Pilih Pegawai <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <select name="user_id" required
                                class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none bg-white">
                            <option value="">-- Pilih User / Pegawai --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                    {{ $user->name }} ({{ $user->nip ?? 'Tanpa NIP' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Device ID --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700">Device ID / UUID <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="text" name="device_id" value="{{ old('device_id') }}"
                               placeholder="Contoh: 8D7F-9A2E-..." required
                               class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-mono">
                    </div>
                    <p class="text-[11px] text-gray-400 italic">Dapatkan ID ini dari informasi perangkat di aplikasi mobile pegawai.</p>
                </div>

                {{-- Status Toggle --}}
                <div class="pt-2">
                    <label class="group flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition-colors">Perangkat Aktif</span>
                            <p class="text-xs text-gray-400">Jika dinonaktifkan, pegawai tidak bisa login/absen dari perangkat ini.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                <a href="{{ route('superadmin.absensi.perangkat-pengguna.index') }}"
                   class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:ring-2 focus:ring-gray-100">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition-all hover:bg-blue-700 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perangkat
                </button>
            </div>
        </div>
    </form>

    {{-- Info Card --}}
    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 flex gap-4 shadow-sm ring-1 ring-blue-200/50">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="space-y-2">
            <div>
                <h4 class="text-sm font-bold text-blue-800">Sistem Pendaftaran Otomatis Aktif!</h4>
                <p class="mt-1 text-xs text-blue-700 leading-relaxed">
                    Sistem pendaftaran perangkat saat ini sudah berjalan secara **otomatis**. Pegawai akan terdaftar secara langsung saat mereka melakukan login pertama kali di aplikasi mobile. 
                </p>
            </div>
            <div class="pt-2 border-t border-blue-200/50 text-xs text-blue-600 font-medium">
                Gunakan form manual ini hanya jika Anda ingin meregistrasikan perangkat secara manual atau melakukan perbaikan data (Override) di luar jalur aplikasi mobile.
            </div>
        </div>
    </div>

</div>
@endsection