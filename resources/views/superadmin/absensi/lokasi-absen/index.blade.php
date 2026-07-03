@extends('layouts.app')

@section('content')
<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Lokasi Absen</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola titik koordinat lokasi yang digunakan untuk validasi absensi pegawai.</p>
        </div>
        <a href="{{ route('superadmin.absensi.lokasi-absen.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Lokasi
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <form method="GET" class="flex flex-col gap-4 p-5 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Cari Lokasi</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $search }}"
                           placeholder="Nama lokasi..."
                           class="w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
            <div class="w-full md:w-56">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">OPD / Unit</label>
                <select name="unit_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Semua --</option>
                    @foreach($units ?? [] as $u)
                        <option value="{{ $u->id }}" @selected((string)$unitId === (string)$u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="rounded-xl bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700">
                    Filter
                </button>
                <a href="{{ route('superadmin.absensi.lokasi-absen.index') }}"
                   class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Nama Lokasi</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Koordinat</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Radius</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 text-gray-600">
                                {{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800">{{ $item->name }}</div>
                                @if($item->unit_id)
                                    <div class="mt-0.5 text-xs text-gray-400">Unit ID: {{ $item->unit_id }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">
                                <div class="flex items-center gap-1">
                                    <span class="text-[10px] font-bold text-gray-400">LAT</span> {{ $item->latitude }}
                                </div>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="text-[10px] font-bold text-gray-400">LNG</span> {{ $item->longitude }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200">
                                    {{ $item->radius_meters }} m
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($item->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-green-700 border border-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                       target="_blank"
                                       title="Buka di Google Maps"
                                       class="rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition-colors">
                                        🗺 Map
                                    </a>
                                    <a href="{{ route('superadmin.absensi.lokasi-absen.edit', $item->id) }}"
                                       class="rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-semibold text-orange-600 hover:bg-orange-100 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('superadmin.absensi.lokasi-absen.destroy', $item->id) }}"
                                          onsubmit="return confirm('Hapus lokasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2 text-gray-400">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-sm text-gray-500">Belum ada lokasi absen yang terdaftar.</span>
                                    <a href="{{ route('superadmin.absensi.lokasi-absen.create') }}"
                                       class="mt-1 text-xs text-blue-500 hover:underline">Tambah Lokasi Pertama →</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="border-t border-gray-100 px-4 py-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection