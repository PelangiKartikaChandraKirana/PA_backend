@extends('layouts.app')

@section('content')
<div class="p-6 space-y-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Prosentase TPP</h1>
            <p class="mt-1 text-sm text-gray-500">Pemantauan persentase kehadiran dan potongan TPP per bulan berdasarkan absensi.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('superadmin.laporan.tpp') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Bulan</label>
                <select name="bulan" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tahun</label>
                <select name="tahun" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @for($i=date('Y')-2; $i<=date('Y'); $i++)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700">
                    Tampilkan & Hitung
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm whitespace-nowrap">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600 border-r border-gray-200" rowspan="2">No</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600 border-r border-gray-200" rowspan="2">Nama</th>
                        <th class="px-4 py-2 text-center font-semibold uppercase text-gray-600 border-b border-r border-gray-200" colspan="5">Rekap Kehadiran (Hari)</th>
                        <th class="px-4 py-2 text-center font-semibold uppercase text-gray-600 border-b border-gray-200" colspan="3">Nilai TPP</th>
                    </tr>
                    <tr>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 border-r border-gray-200">Hadir</th>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 border-r border-gray-200">Izin/Sakit</th>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-red-500 border-r border-gray-200" title="Tanpa Keterangan">TK/Alfa</th>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-orange-500 border-r border-gray-200" title="Terkait Keterlambatan">Terlambat<br><span class="text-[10px] font-normal">&le;30m | >30m</span></th>
                        <th class="px-3 py-2 text-center text-xs font-semibold text-orange-500 border-r border-gray-200" title="Terkait Pulang Cepat">Pulang Cepat<br><span class="text-[10px] font-normal">&le;30m | >30m</span></th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500">Pagu Asli</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-red-500">Potongan (%)</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-green-600">Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($tppData as $index => $row)
                        @php
                            $isAlert = $row->penguranganPercent > 10;
                            $bgRow = $isAlert ? 'bg-red-50' : 'hover:bg-gray-50';
                        @endphp
                        <tr class="{{ $bgRow }}">
                            <td class="px-4 py-3 text-gray-700 border-r border-gray-100">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 border-r border-gray-100">
                                {{ $row->employee->name }}
                                @if($isAlert)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                        Perlu Evaluasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-gray-800 text-center border-r border-gray-100 font-medium">
                                {{ $row->statHadir }}
                            </td>
                            <td class="px-3 py-3 text-gray-600 text-center border-r border-gray-100">
                                {{ $row->statIzin > 0 ? $row->statIzin : '-' }}
                            </td>
                            <td class="px-3 py-3 text-red-600 text-center border-r border-gray-100 font-bold">
                                {{ ($row->statTK > 0 || $row->statLupaAbsen > 0) ? ($row->statTK + $row->statLupaAbsen) : '-' }}
                                @if($row->statLupaAbsen > 0)
                                    <div class="text-[10px] font-normal text-red-400">({{ $row->statLupaAbsen }} Lupa)</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-orange-600 text-center border-r border-gray-100">
                                @if($row->statTerlambatMax30 > 0 || $row->statTerlambatLebih30 > 0)
                                    {{ $row->statTerlambatMax30 }}x <span class="text-gray-300">|</span> <b>{{ $row->statTerlambatLebih30 }}x</b>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-orange-600 text-center border-r border-gray-100">
                                @if($row->statPulangCepatMax30 > 0 || $row->statPulangCepatLebih30 > 0)
                                    {{ $row->statPulangCepatMax30 }}x <span class="text-gray-300">|</span> <b>{{ $row->statPulangCepatLebih30 }}x</b>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            
                            <td class="px-3 py-3 text-gray-500 text-right">Rp{{ number_format($row->tppAllowance, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-red-600 text-right font-bold">
                                -{{ number_format($row->penguranganPercent, 1) }}%
                                <div class="text-[10px] font-normal text-red-400 mt-0.5 whitespace-nowrap">(-Rp{{ number_format($row->totalPotongan, 0, ',', '.') }})</div>
                            </td>
                            <td class="px-3 py-3 text-green-700 text-right font-bold text-base">Rp{{ number_format($row->tppDiterima, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">Belum ada data/pegawai untuk bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
