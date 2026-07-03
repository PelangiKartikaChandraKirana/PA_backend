<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

        <!-- HEADER & FILTER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                    Monitoring Presensi
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    @if($unitKerja)
                        Unit Kerja/Instansi: <span class="font-medium text-slate-700">{{ $unitKerja }}</span>
                    @else
                        Semua Unit Kerja
                    @endif
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <form action="{{ route('admin.monitoring') }}" method="GET" class="flex gap-2">
                    <input type="month" name="month" value="{{ $selectedMonth }}" class="bg-white border border-slate-200 rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Filter
                    </button>
                </form>
                <a href="{{ route('admin.monitoring.export', ['month' => $selectedMonth]) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-sm shadow-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- STATS OVERVIEW -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- TOTAL KEHADIRAN -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm">
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-2">Total Presensi</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-black text-slate-800">{{ number_format($stats['total_logs']) }}</h3>
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- TEPAT WAKTU -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm">
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-2">Tepat Waktu</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-black text-emerald-600">{{ number_format($stats['ontime']) }}</h3>
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- TERLAMBAT -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm">
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-2">Terlambat</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-black text-amber-600">{{ number_format($stats['late']) }}</h3>
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- IZIN / SAKIT -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm">
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-2">Izin/Sakit</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-black text-rose-600">{{ number_format($stats['absent']) }}</h3>
                    <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE DATA -->
        <div class="bg-white shadow-sm rounded-2xl border border-slate-200/60 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200/60 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Rekap Presensi Bulanan</h3>
                <span class="text-xs px-3 py-1.5 bg-slate-50 border border-slate-200/60 text-slate-600 rounded-md font-medium tracking-wide">
                    {{ $data->total() }} Pegawai
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/60">
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">No</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Pegawai</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Hadir</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Alpha</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Cuti/Izin</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $index => $user)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                                    {{ $data->firstItem() + $index }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="hidden sm:flex w-9 h-9 rounded-full bg-slate-100 text-slate-600 items-center justify-center font-bold text-xs ring-1 ring-slate-200/60">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                NIP. {{ $user->employee?->nip ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60">
                                        {{ $user->stats->hari_kerja }} Hari
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg {{ $user->stats->alpha > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200/60 font-bold' : 'text-slate-400 font-medium' }}">
                                        {{ $user->stats->alpha }} Hari
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg {{ $user->stats->cuti_izin > 0 ? 'bg-purple-50 text-purple-700 border border-purple-200/60 font-bold' : 'text-slate-400 font-medium' }}">
                                        {{ $user->stats->cuti_izin }} Hari
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg {{ $user->stats->terlambat > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200/60 font-bold' : 'text-slate-400 font-medium' }}">
                                        {{ $user->stats->terlambat }} Kali
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 flex items-center justify-center rounded-2xl mb-4 border border-slate-200/60">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <h3 class="text-slate-800 font-bold mb-1">Riwayat Kosong</h3>
                                        <p class="text-slate-500 text-sm max-w-sm mx-auto">Tidak ada presensi pada periode bulan ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- PAGINATION -->
            @if($data->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-white">
                    {{ $data->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
