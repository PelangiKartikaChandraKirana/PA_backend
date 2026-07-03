@extends('layouts.app')

@section('content')
<div class="px-6 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Data Pegawai</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Kelola seluruh informasi staf dan pejabat dalam sistem.</p>
            </div>
        </div>

        <a href="{{ route('superadmin.pegawai.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pegawai
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm animate-in fade-in slide-in-from-top-4">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Section -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">No</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Identitas</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Jabatan</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Unit Kerja</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Status</th>
                        <th class="px-6 py-4 text-right font-bold uppercase tracking-widest text-slate-500 text-[10px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employees as $employee)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-medium italic">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-sm">{{ $employee->name }}</span>
                                    <span class="text-xs font-semibold text-slate-400 mt-0.5 tracking-tight">NIP. {{ $employee->nip }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-700 font-medium">{{ $employee->position->name ?? '-' }}</span>
                                    <span class="text-[10px] uppercase font-bold text-blue-500/70 tracking-tighter">{{ $employee->employeeType->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <span class="bg-slate-100 px-2 py-1 rounded text-xs font-semibold">{{ $employee->department->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $status = $employee->status ?? 'Aktif';
                                    $isActive = strtolower($status) === 'aktif';
                                @endphp
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $isActive ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20' : 'bg-slate-50 text-slate-500 ring-1 ring-slate-400/20' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Edit -->
                                    <a href="{{ route('superadmin.pegawai.edit', $employee->id) }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-100 transition hover:bg-amber-500 hover:text-white"
                                       title="Edit Data">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5M15.172 2.757a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682 10.682-10.682z"></path></svg>
                                    </a>

                                    <!-- Wajah -->
                                    <a href="{{ route('superadmin.pegawai.wajah') }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 border border-sky-100 transition hover:bg-sky-500 hover:text-white"
                                       title="Kelola Wajah">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </a>

                                    <!-- Hapus -->
                                    <form action="{{ route('superadmin.pegawai.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Hapus data pegawai ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 border border-rose-100 transition hover:bg-rose-500 hover:text-white"
                                            title="Hapus Pegawai">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-slate-50 flex items-center justify-center rounded-2xl mb-4 border border-slate-200/60">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <h3 class="text-slate-800 font-bold mb-1">Daftar Pegawai Kosong</h3>
                                    <p class="text-slate-500 text-sm max-w-sm mx-auto">Klik tombol <b>Tambah Pegawai</b> untuk mendaftarkan staf baru ke dalam sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection