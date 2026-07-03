@extends('layouts.app')

@section('content')
<div class="px-6 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Kelola akun, hak akses, dan monitoring keamanan pengguna sistem.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
             <a href="{{ route('superadmin.pengguna.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna
            </a>

            <div class="h-8 w-px bg-slate-200 mx-1 hidden sm:block"></div>

            <a href="{{ route('superadmin.pengguna.export') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('superadmin.pengguna.index') }}" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-5">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Pencarian</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Username / NIP / Nama..."
                           class="w-full rounded-xl border-slate-200 pl-9 pr-4 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Role Akses</label>
                <select name="role" class="w-full rounded-xl border-slate-200 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pegawai" {{ request('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Status Aku</label>
                <select name="status" class="w-full rounded-xl border-slate-200 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif Only</option>
                    <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif Only</option>
                </select>
            </div>

            <div class="space-y-1.5 lg:col-span-2 flex items-end gap-2">
                <div class="flex-1 space-y-1.5">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Filter OPD</label>
                    <input type="text" name="opd" value="{{ request('opd') }}" placeholder="Contoh: Dinas Kesehatan"
                           class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="h-[38px] px-5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition active:scale-95">Filter</button>
                    <a href="{{ route('superadmin.pengguna.index') }}" class="h-[38px] px-5 flex items-center rounded-xl bg-slate-50 text-slate-600 border border-slate-200 text-xs font-bold hover:bg-slate-100 transition">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <form action="{{ route('superadmin.pengguna.bulk-status') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <input type="checkbox" id="checkAll" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Profil Pengguna</th>
                            <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">NIP & Role</th>
                            <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">OPD / Unit Kerja</th>
                            <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Status</th>
                            <th class="px-6 py-4 text-right font-bold uppercase tracking-widest text-slate-500 text-[10px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($users as $user)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-slate-400 uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 text-sm tracking-tight leading-none">{{ $user->name }}</span>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    {{ $user->username }}
                                                </span>
                                                <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                                <span class="text-[10px] font-bold text-blue-500 tracking-tighter uppercase">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5">
                                        <span class="text-xs font-bold text-slate-600">NIP. {{ $user->nip ?: 'NOT_SET' }}</span>
                                        <div class="flex">
                                            @if($user->role === 'superadmin')
                                                <span class="inline-flex items-center gap-1 rounded bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase text-indigo-600 ring-1 ring-indigo-500/20 tracking-widest">
                                                    SUPERADMIN
                                                </span>
                                            @elseif($user->role === 'admin')
                                                <span class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-0.5 text-[10px] font-black uppercase text-blue-600 ring-1 ring-blue-500/20 tracking-widest">
                                                    ADMIN
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-[10px] font-black uppercase text-slate-600 ring-1 ring-slate-400/20 tracking-widest">
                                                    PEGAWAI
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    {{ $user->unit_kerja ?: '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->status === 'Aktif')
                                        <span class="inline-flex rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-600 ring-1 ring-emerald-500/20">AKTIF</span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-rose-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-rose-600 ring-1 ring-rose-500/20">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('superadmin.pengguna.edit', $user->id) }}"
                                           class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 transition hover:bg-indigo-600 hover:text-white"
                                           title="Kelola Akun">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </a>

                                        @if($user->role !== 'superadmin')
                                        <form action="{{ route('superadmin.pengguna.delete', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini dari sistem?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 border border-rose-100 transition hover:bg-rose-600 hover:text-white">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500 italic">Data pengguna tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <div class="flex gap-2">
                    <button type="submit" name="status" value="Aktif" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-black border border-emerald-100 uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition">Aktifkan Massal</button>
                    <button type="submit" name="status" value="Nonaktif" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-[10px] font-black border border-rose-100 uppercase tracking-widest hover:bg-rose-600 hover:text-white transition">Nonaktifkan Massal</button>
                </div>
                @if(method_exists($users, 'links'))
                    <div class="flex-1 max-w-sm ml-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = checkAll.checked;
                });
            });
        }
    });
</script>
@endsection