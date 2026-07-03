@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Detail Akses Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">
                Informasi hak akses dan peran untuk pengguna
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
            <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->status }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-700">Ringkasan Hak Akses</h2>
        <p class="mt-2 text-sm text-slate-500">
            Halaman ini menampilkan ringkasan akses berdasarkan role pengguna.
            Saat ini sistem masih menggunakan role utama dan belum memakai permission custom per menu.
        </p>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-700">Role Aktif</p>
                <p class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                    {{ $user->role }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-700">Status Akun</p>
                @if($user->status === 'Aktif')
                    <p class="mt-2 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                        Aktif
                    </p>
                @else
                    <p class="mt-2 inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                        Nonaktif
                    </p>
                @endif
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
            <div class="bg-slate-50 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-700">Estimasi Akses Berdasarkan Role</h3>
            </div>

            <div class="p-4">
                @if($user->role === 'superadmin')
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>• Akses penuh ke seluruh modul sistem</li>
                        <li>• Kelola pegawai, pengguna, absensi, master, dan konfigurasi</li>
                        <li>• Reset password, ubah status user, export/import data</li>
                    </ul>
                @elseif($user->role === 'admin')
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>• Akses modul administratif sesuai kebijakan sistem</li>
                        <li>• Dapat mengelola data operasional tertentu</li>
                        <li>• Hak akses penuh belum setara superadmin</li>
                    </ul>
                @else
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>• Akses terbatas sesuai peran pegawai</li>
                        <li>• Umumnya hanya untuk penggunaan fitur personal / operasional tertentu</li>
                        <li>• Tidak memiliki akses administrasi penuh</li>
                    </ul>
                @endif
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
            Ke depan, halaman ini bisa dikembangkan untuk menampilkan permission detail per menu,
            role custom, dan override akses khusus per user.
        </div>
    </div>
</div>
@endsection