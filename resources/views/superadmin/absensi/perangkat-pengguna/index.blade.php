@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Perangkat Pengguna</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola dan monitor audit perangkat (Device Binding) yang tertaut ke akun pegawai.</p>
        </div>
        <a href="{{ route('superadmin.absensi.perangkat-pengguna.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-95">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Perangkat
        </a>
    </div>

    {{-- Analytics Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Total Perangkat</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Aktif --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-green-50 p-3 text-green-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Perangkat Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['active']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Inaktif --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Inaktif / Blocked</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['inactive']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Users Without Device --}}
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-5 shadow-sm ring-1 ring-indigo-100/50">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-indigo-600 p-3 text-white shadow-lg shadow-indigo-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">User Belum Taut (Audit)</p>
                    <h3 class="text-2xl font-black text-indigo-700">{{ number_format($stats['users_no_device']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md">
        <form method="GET" class="flex flex-col gap-4 p-5 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Cari Pegawai / NIP</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Ketik nama atau NIP..."
                           class="w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all bg-gray-50/30">
                </div>
            </div>
            <div class="w-full md:w-48">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Status</label>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all bg-white appearance-none">
                    <option value="">-- Semua --</option>
                    <option value="1" @selected($status == '1')>Aktif</option>
                    <option value="0" @selected($status == '0')>Nonaktif / Terblokir</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-black active:scale-95">
                    Filter
                </button>
                <a href="{{ route('superadmin.absensi.perangkat-pengguna.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-600 transition-all hover:bg-gray-50 active:scale-95">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Main Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-4 py-4 text-left font-bold uppercase tracking-wider text-gray-500">No</th>
                        <th class="px-4 py-4 text-left font-bold uppercase tracking-wider text-gray-500">Pemilik & Unit</th>
                        <th class="px-4 py-4 text-left font-bold uppercase tracking-wider text-gray-500">Identitas Perangkat</th>
                        <th class="px-4 py-4 text-left font-bold uppercase tracking-wider text-gray-500">Tgl Registrasi</th>
                        <th class="px-4 py-4 text-left font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-4 text-right font-bold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($items as $item)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                            <td class="px-4 py-4 text-gray-400 font-medium">
                                {{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold uppercase">
                                        {{ substr($item->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $item->user->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->user->nip ?? 'NIP Kosong' }}</div>
                                        <div class="mt-0.5 inline-block text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded uppercase font-bold">
                                            {{ $item->user->unit_kerja ?? 'Unit Tidak Set' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="group relative">
                                    <div class="font-mono text-[11px] bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-gray-600 shadow-inner group-hover:border-blue-300 transition-colors">
                                        {{ $item->device_id }}
                                    </div>
                                    @if(strlen($item->device_id) > 15)
                                        <span class="absolute -top-6 left-0 hidden group-hover:block bg-gray-900 text-white text-[10px] px-2 py-1 rounded shadow-lg whitespace-nowrap z-10">
                                            UUID Perangkat Android/iOS Terdeteksi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $item->registered_at?->format('d M Y') }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 pl-5">Jam {{ $item->registered_at?->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-4 py-4">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-green-700 border border-green-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-red-600 border border-red-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Terblokir
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2 text-right">
                                    <a href="{{ route('superadmin.absensi.perangkat-pengguna.edit', $item->id) }}"
                                       class="rounded-lg bg-white border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-slate-50 transition-all hover:text-blue-600 active:scale-95 shadow-sm">
                                        Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('superadmin.absensi.perangkat-pengguna.destroy', $item->id) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus/lepas tautan perangkat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg bg-red-50 border border-red-100 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-400">
                                    <div class="rounded-full bg-gray-100 p-4">
                                        <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="max-w-xs mx-auto text-center">
                                        <h4 class="text-sm font-bold text-gray-800">Perangkat Tidak Ditemukan</h4>
                                        <p class="text-xs text-gray-500 mt-1">Coba sesuaikan filter pencarian atau ajak pegawai untuk login pertama kali di aplikasi.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="border-t border-gray-100 bg-gray-50/30 px-6 py-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection