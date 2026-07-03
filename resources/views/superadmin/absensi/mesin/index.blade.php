@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Monitor & Manajemen Mesin</h1>
            <p class="mt-1 text-sm text-gray-500">Pantau status kesehatan dan konektivitas mesin absensi fingerprint secara real-time.</p>
        </div>
        <a href="{{ route('superadmin.absensi.mesin.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-95">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Daftarkan Mesin
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Analytics Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-slate-50 p-3 text-slate-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Mesin</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Online --}}
        <div class="rounded-2xl border border-green-100 bg-green-50/30 p-5 shadow-sm ring-1 ring-green-100">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-green-500 p-3 text-white shadow-lg shadow-green-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.313 7.636c6.013-6.013 15.361-6.013 21.374 0"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-green-600">Live Online</p>
                    <h3 class="text-2xl font-black text-green-700">{{ number_format($stats['online']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Offline --}}
        <div class="rounded-2xl border border-red-100 bg-red-50/30 p-5 shadow-sm ring-1 ring-red-100">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-red-500 p-3 text-white shadow-lg shadow-red-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M5.636 5.636a9 9 0 0112.728 0m-12.728 0L8.465 8.465m-2.829-2.829L3 3M9.172 9.172a4 4 0 015.656 0m-5.656 0l-2.828-2.828m2.828 2.828L3 21"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-red-600">Offline / Problem</p>
                    <h3 class="text-2xl font-black text-red-700">{{ number_format($stats['offline']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Inactive --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-gray-100 p-3 text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Suspended</p>
                    <h3 class="text-2xl font-black text-gray-400">{{ number_format($stats['inactive']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md">
        <form method="GET" class="flex flex-col gap-4 p-5 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Cari Mesin / IP / Serial</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Ketik identitas mesin..."
                           class="w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all bg-gray-50/30">
                </div>
            </div>
            <div class="w-full md:w-64">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">OPD / Unit Penempatan</label>
                <select name="unit_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 appearance-none bg-white">
                    <option value="">-- Semua Unit --</option>
                    @foreach($units ?? [] as $u)
                        <option value="{{ $u->id }}" @selected((string)$unitId === (string)$u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-black active:scale-95 transition-all">
                    Filter
                </button>
                <a href="{{ route('superadmin.absensi.mesin.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 active:scale-95 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Main Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80 font-bold text-gray-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4 text-left">Identitas Mesin</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider">Jaringan & IP</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider">Unit / Lokasi</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider">Last Heartbeat</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-wider">Health Status</th>
                        <th class="px-6 py-4 text-right font-bold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($items as $item)
                        @php
                            $online = $item->last_seen_at && now()->diffInMinutes($item->last_seen_at) <= 5;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 tracking-tight">{{ $item->name }}</div>
                                <div class="text-[10px] font-mono text-gray-400 mt-0.5">SN: {{ $item->serial_number ?? 'BLANK' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-lg bg-gray-100 px-2 py-1 font-mono text-[11px] text-gray-600 border border-gray-200">
                                    {{ $item->ip_address ?? '0.0.0.0' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[12px] font-bold text-gray-700 tracking-tight">{{ $item->location_name ?? 'Lokasi Umum' }}</div>
                                <div class="text-[10px] text-indigo-500 font-bold uppercase mt-0.5">{{ $item->unit_name ?? 'No Unit Assigned' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->last_seen_at)
                                    <div class="text-[12px] font-medium text-gray-600">{{ $item->last_seen_at->format('d M, H:i') }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5 italic">{{ $item->last_seen_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-[11px] text-gray-300 italic">Never seen</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!$item->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-500 border border-gray-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Suspended
                                    </span>
                                @else
                                    @if($online)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-green-700 border border-green-200 shadow-sm">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse ring-2 ring-green-200"></span>
                                            Online
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-red-700 border border-red-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Offline
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.absensi.mesin.edit', $item->id) }}"
                                       class="rounded-lg bg-white border border-gray-200 px-3 py-1.5 text-[11px] font-bold text-gray-600 hover:border-blue-300 hover:text-blue-600 shadow-sm transition-all active:scale-95">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.absensi.mesin.destroy', $item->id) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus mesin ini dari monitoring?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg bg-red-50 border border-red-100 px-3 py-1.5 text-[11px] font-bold text-red-600 hover:bg-red-600 hover:text-white transition-all active:scale-95">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-400">
                                    <div class="rounded-full bg-gray-50 p-6 border border-gray-100">
                                        <svg class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                        </svg>
                                    </div>
                                    <div class="max-w-xs mx-auto">
                                        <h4 class="text-sm font-bold text-gray-800">Belum Ada Mesin Terdaftar</h4>
                                        <p class="text-xs text-gray-500 mt-1">Daftarkan mesin fingerprint Anda untuk mulai melakukan monitoring kesehatan perangkat.</p>
                                    </div>
                                    <a href="{{ route('superadmin.absensi.mesin.create') }}"
                                       class="mt-3 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-100 hover:bg-blue-700 active:scale-95 transition-all">Daftarkan Sekarang</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection