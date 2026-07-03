@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/50 pb-12">
    <!-- Header Section -->
    <div class="relative overflow-hidden bg-white px-6 py-8 shadow-sm border-b border-slate-200 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-indigo-50/30 opacity-50"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-md shadow-blue-200 overflow-hidden border border-slate-100 p-1">
                    <img src="{{ asset('images/logo-kominfo-copy.jpg') }}" alt="Logo Kominfo" class="h-full w-full object-contain">
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Dashboard SIAPMAN</h1>
                    <p class="text-sm font-medium text-slate-500 mt-1">Ringkasan Operasional & Monitoring Presensi</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="flex items-center gap-2 rounded-xl bg-white p-1 ring-1 ring-slate-200 shadow-sm">
                    <input
                        type="date"
                        name="tanggal"
                        value="{{ $tanggal }}"
                        class="rounded-lg border-0 bg-transparent px-3 py-1.5 text-sm font-medium text-slate-700 focus:ring-0"
                    >
                    <button type="submit" class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition">
                        Filter Tanggal
                    </button>
                </form>

                <div class="hidden md:flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 ring-1 ring-blue-100 border border-blue-200 shadow-sm">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700 truncate max-wxs">DINAS KOMINFO</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8 space-y-8">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($summaryCards as $card)
                @php
                    $colors = [
                        'cyan' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'icon' => 'text-cyan-500', 'border' => 'border-cyan-200', 'ring' => 'group-hover:ring-cyan-400'],
                        'sky' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'icon' => 'text-sky-500', 'border' => 'border-sky-200', 'ring' => 'group-hover:ring-sky-400'],
                        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'icon' => 'text-emerald-500', 'border' => 'border-emerald-200', 'ring' => 'group-hover:ring-emerald-400'],
                        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'icon' => 'text-indigo-500', 'border' => 'border-indigo-200', 'ring' => 'group-hover:ring-indigo-400'],
                        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'icon' => 'text-rose-500', 'border' => 'border-rose-200', 'ring' => 'group-hover:ring-rose-400'],
                    ];
                    $theme = $colors[$card['accent']] ?? $colors['sky'];
                    $isNumeric = is_numeric($card['value']);
                @endphp

                <a href="{{ $card['route'] }}" class="group relative overflow-hidden rounded-2xl bg-white p-6 border border-slate-200 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md ring-1 ring-transparent {{ $theme['ring'] }}">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $theme['bg'] }} opacity-50 blur-2xl"></div>
                    
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $card['title'] }}</p>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $theme['bg'] }} {{ $theme['border'] }} border">
                            <svg class="h-4 w-4 {{ $theme['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <p class="{{ $isNumeric ? 'text-4xl' : 'text-xl leading-tight' }} font-extrabold text-slate-800 line-clamp-2">
                                {{ $card['value'] }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-slate-500">{{ $card['subtitle'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Middle Row: Modules & Charts -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Ringkasan Hari Ini -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm flex flex-col justify-between">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Ringkasan Kehadiran</h2>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">Status operasional hari berjalan</p>
                        </div>
                        <a href="{{ route('superadmin.laporan.presensi-harian', ['tanggal' => $tanggal]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Sudah Hadir</p>
                        <p class="text-3xl font-black text-emerald-700">{{ $hadirHariIni }}</p>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-4 border border-amber-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-1">Terlambat</p>
                        <p class="text-3xl font-black text-amber-700">{{ $terlambatHariIni }}</p>
                    </div>
                    <div class="rounded-xl bg-rose-50 p-4 border border-rose-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600 mb-1">Belum Hadir</p>
                        <p class="text-3xl font-black text-rose-700">{{ $belumPresensi }}</p>
                    </div>
                    <div class="rounded-xl bg-indigo-50 p-4 border border-indigo-100">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 mb-1">Kendala Dlm Proses</p>
                        <p class="text-3xl font-black text-indigo-700">{{ $pendingKendala }}</p>
                    </div>
                </div>
            </div>

            <!-- Koneksi Mesin -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm flex flex-col">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Status Mesin</h2>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="flex items-center text-xs font-medium text-slate-500"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> {{ $mesinOnline }} Online</span>
                                <span class="flex items-center text-xs font-medium text-slate-500"><span class="w-2 h-2 rounded-full bg-slate-300 mr-1.5"></span> {{ $mesinOffline }} Offline</span>
                            </div>
                        </div>
                        <a href="{{ route('superadmin.absensi.mesin.index') }}" class="text-xs font-semibold text-blue-600 transition hover:text-blue-800">Kelola</a>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[220px]">
                    <div class="space-y-3">
                        @forelse($machines as $machine)
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 border border-slate-200">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{ $machine['name'] }}</span>
                                </div>
                                @if($machine['status'])
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-600 ring-1 ring-inset ring-emerald-500/20">Online</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 text-[10px] font-bold text-slate-600 ring-1 ring-inset ring-slate-500/20">Offline</span>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 py-8 text-center bg-slate-50">
                                <p class="text-sm font-medium text-slate-500">Belum ada mesin absensi</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Access / Info -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm flex flex-col">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                    <h2 class="text-lg font-bold text-slate-800">Akses Cepat Modul</h2>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">Navigasi langsung administrasi</p>
                </div>
                
                <div class="p-6 grid grid-cols-2 gap-3">
                    <a href="{{ route('superadmin.pegawai.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-blue-300 hover:shadow">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="text-xs font-semibold text-slate-700">Pegawai</span>
                    </a>
                    <a href="{{ route('superadmin.pengguna.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-blue-300 hover:shadow">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        <span class="text-xs font-semibold text-slate-700">Pengguna</span>
                    </a>
                    <a href="{{ route('superadmin.hari-libur.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-blue-300 hover:shadow">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-semibold text-slate-700">Hari Libur</span>
                    </a>
                    <a href="{{ route('superadmin.absensi.jadwal-kerja.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-blue-300 hover:shadow">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-xs font-semibold text-slate-700">Jadwal</span>
                    </a>
                </div>

                <!-- Info Hari libur di bawah menu akses cepat -->
                <div class="px-6 pb-6 pt-2">
                    <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-rose-600">Info Libur Terdekat</p>
                        </div>
                        @if($hariLiburTerdekat)
                            <p class="text-sm font-extrabold text-slate-800">{{ $hariLiburTerdekat->name }}</p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($hariLiburTerdekat->date)->translatedFormat('d F Y') }}</p>
                        @else
                            <p class="text-xs font-medium text-slate-500 italic mt-1">Belum ada jadwal libur.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Tables -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            
            <!-- Log Presensi Terbaru -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Log Presensi Terbaru</h2>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Pantauan rill time aktivitas kehadiran masuk</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-600 ring-1 ring-inset ring-blue-500/20">Live</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50/50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-xs tracking-wider uppercase">Nama Pegawai</th>
                                <th class="px-6 py-3 font-semibold text-xs tracking-wider uppercase">NIP</th>
                                <th class="px-6 py-3 font-semibold text-xs tracking-wider uppercase text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentAttendances as $item)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-6 py-3.5">
                                        <div class="font-medium text-slate-800">{{ $item->name }}</div>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500">{{ $item->nip ?? '-' }}</td>
                                    <td class="px-6 py-3.5 text-right font-medium text-indigo-600">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 mb-3">
                                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500">Belum ada aktivitas presensi sejauh ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pegawai Belum Hadir -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Daftar Belum Presensi</h2>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Sebagian pegawai yang belum tercatat hadir</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50/50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-xs tracking-wider uppercase">Nama Pegawai</th>
                                <th class="px-6 py-3 font-semibold text-xs tracking-wider uppercase text-right">NIP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pegawaiBelumPresensi as $pegawai)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-2 w-2 rounded-full bg-rose-500"></div>
                                            <span class="font-medium text-slate-800">{{ $pegawai->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-slate-500 font-medium font-mono text-xs">
                                        {{ $pegawai->nip ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-12 text-center border-b border-transparent">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 mb-3">
                                            <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <p class="text-sm font-medium text-emerald-600">Luar Biasa!</p>
                                        <p class="text-xs text-slate-500 mt-1">Semua pegawai telah mengisi presensi kehadiran.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection