@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Laporan Presensi Harian</h1>
            <p class="mt-1 text-sm text-slate-500">Rekap kehadiran pegawai per hari.</p>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('superadmin.laporan.presensi-harian') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-3 text-sm text-white hover:bg-blue-700">
                    Filter
                </button>

                <a href="{{ route('superadmin.laporan.presensi-harian') }}"
                    class="rounded-xl border border-slate-300 px-5 py-3 text-sm text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
                
                <button type="submit" name="export" value="excel" class="ml-auto rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-700 flex items-center gap-2 shadow-sm shadow-emerald-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-700">Data Presensi Harian</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">NIP / Nomor Induk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Unit Kerja / OPD</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jam Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jam Keluar</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Durasi Kerja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Validasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Alasan Validasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Keterlambatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Lokasi / Mesin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">IP / Device</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Foto Capture</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($data as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $row->employee_id_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $row->employee_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->position_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->company_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->jam_masuk }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->jam_keluar }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->durasi_kerja }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->status }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if(($row->validation_status ?? 'VALID') == 'VALID')
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">
                                        VALID
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-700">
                                        REJECTED
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                @switch($row->validation_reason ?? null)
                                    @case('OUTSIDE_GEOFENCE')
                                        Di luar geofence
                                        @break

                                    @case('TIME_NOT_SYNC')
                                        Waktu tidak sinkron
                                        @break

                                    @default
                                        -
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->keterlambatan }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->lokasi_mesin }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row->ip_address }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($row->foto_capture)
                                    <a href="{{ $row->foto_capture }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 transition hover:text-blue-800 hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Lihat Foto
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada data presensi harian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
