@extends('layouts.app')

@section('content')
@php
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp
<div class="px-6 py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Laporan Presensi Bulanan</h1>
        <p class="mt-2 text-sm text-slate-500">Rekap presensi per pegawai berdasarkan bulan dan tahun.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('superadmin.laporan.presensi-bulanan') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Bulan</label>
                <select name="bulan" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    @foreach($months as $num => $label)
                        <option value="{{ $num }}" {{ (int) $bulan === (int) $num ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tahun</label>
                <select name="tahun" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ (int) $tahun === (int) $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-2">
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm text-white hover:bg-blue-700">Filter</button>
                <a href="{{ route('superadmin.laporan.presensi-bulanan') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
                <button type="submit" name="export" value="excel" class="ml-auto rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-700 flex items-center gap-2 shadow-sm shadow-emerald-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Pegawai</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalPegawai }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Hadir</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $totalHadir }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Tidak Hadir (Alpha)</p>
            <p class="mt-2 text-3xl font-bold text-rose-600">{{ $totalTidakHadir }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Cuti / Izin</p>
            <p class="mt-2 text-3xl font-bold text-purple-600">{{ $totalCuti }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Terlambat</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $totalTerlambat }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Pulang Sebelum Waktu</p>
            <p class="mt-2 text-3xl font-bold text-rose-600">{{ $totalPulangCepat }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-700">Rekap Bulanan - {{ $months[(int) $bulan] }} {{ $tahun }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">NIP / Nomor Induk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Unit Kerja / OPD</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Hari Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Alpha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Cuti/Izin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL1</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL2</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL3</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL4</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW1</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW2</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW3</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW4</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Rata-rata Durasi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $row->employee_id_number }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $row->employee_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->company_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->hari_kerja }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 text-center font-bold {{ $row->tidak_hadir > 0 ? 'text-red-500' : '' }}">{{ $row->tidak_hadir }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 text-center font-bold {{ $row->cuti_izin > 0 ? 'text-purple-500' : '' }}">{{ $row->cuti_izin }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl1 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl2 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl3 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl4 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw1 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw2 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw3 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw4 }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->avg_durasi }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data presensi bulanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection