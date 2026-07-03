@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Lokasi Absen Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">
                Pengaturan lokasi absen khusus untuk pengguna
                <span class="font-semibold text-slate-700">{{ $user->name }}</span>.
            </p>
        </div>

        <a href="{{ route('superadmin.pengguna.index') }}"
           class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Nama</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->name }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Username</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->username }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Role</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->role }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">OPD / Unit Kerja</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->unit_kerja ?? '-' }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-700">Informasi Lokasi Absen</h2>
        <p class="mt-2 text-sm text-slate-500">
            Halaman ini disiapkan untuk pengaturan lokasi absen khusus per pengguna.
            Saat ini fitur detail lokasi absen per user belum dihubungkan ke tabel atau form pengaturan.
        </p>

        <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
            Belum ada data lokasi absen khusus untuk pengguna ini.
        </div>
    </div>
</div>
@endsection