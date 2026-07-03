<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                    Data Presensi Pegawai
                </h2>
                <p class="text-sm text-slate-500 mt-1 font-medium">
                    Hari Ini: <span class="text-slate-700">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span>
                </p>
            </div>
            
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100/80 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 focus:ring-4 focus:ring-slate-100">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Segarkan Data
                </button>
            </div>
        </div>

        <!-- TABLE DATA -->
        <div class="bg-white shadow-sm rounded-2xl border border-slate-200/60 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200/60 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Kehadiran Hari Ini</h3>
                <span class="text-xs px-3 py-1.5 bg-slate-50 border border-slate-200/60 text-slate-600 rounded-md font-medium tracking-wide">
                    {{ count($data) }} Presensi
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/60">
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">No</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Pegawai</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Check In</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Check Out</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($data as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-200 group">
                                <td class="px-6 py-4 text-sm text-slate-500 font-medium whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm ring-1 ring-slate-200/60">
                                            {{ substr($item->user->name ?? '?', 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $item->user->name ?? '-' }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                NIP. {{ $item->employee?->nip ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg {{ $item->log && $item->log->check_in_at ? 'bg-slate-50 text-slate-700 border border-slate-200/60' : 'text-slate-400' }}">
                                            <span class="text-sm font-medium">
                                                {{ $item->log && $item->log->check_in_at ? \Carbon\Carbon::parse($item->log->check_in_at)->format('H:i') : '--:--' }}
                                            </span>
                                        </div>
                                        @if($item->log && $item->log->check_in_photo_path)
                                            <a href="{{ Storage::url($item->log->check_in_photo_path) }}" target="_blank" class="text-[11px] font-medium text-blue-600 hover:text-blue-800 mt-1.5 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                                Lihat Bukti Foto
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg {{ $item->log && $item->log->check_out_at ? 'bg-slate-50 text-slate-700 border border-slate-200/60' : 'text-slate-400' }}">
                                            <span class="text-sm font-medium">
                                                {{ $item->log && $item->log->check_out_at ? \Carbon\Carbon::parse($item->log->check_out_at)->format('H:i') : '--:--' }}
                                            </span>
                                        </div>
                                        @if($item->log && $item->log->check_out_photo_path)
                                            <a href="{{ Storage::url($item->log->check_out_photo_path) }}" target="_blank" class="text-[11px] font-medium text-blue-600 hover:text-blue-800 mt-1.5 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                                Lihat Bukti Foto
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusText = $item->status;
                                        if (stripos($statusText, 'hadir') !== false) {
                                            $badgeColor = 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20';
                                        } elseif (stripos($statusText, 'terlambat') !== false) {
                                            $badgeColor = 'bg-amber-50 text-amber-600 ring-1 ring-amber-500/20';
                                        } elseif (stripos($statusText, 'alpha') !== false) {
                                            $badgeColor = 'bg-rose-50 text-rose-600 ring-1 ring-rose-500/20';
                                        } elseif (stripos($statusText, 'cuti') !== false || stripos($statusText, 'izin') !== false || stripos($statusText, 'sakit') !== false) {
                                            $badgeColor = 'bg-purple-50 text-purple-600 ring-1 ring-purple-500/20';
                                        } else {
                                            $badgeColor = 'bg-slate-50 text-slate-500 ring-1 ring-slate-500/20';
                                        }
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-md shadow-sm {{ $badgeColor }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 flex items-center justify-center rounded-2xl mb-4 border border-slate-200/60">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <h3 class="text-slate-800 font-bold mb-1">Presensi Hari Ini Kosong</h3>
                                        <p class="text-slate-500 text-sm max-w-sm mx-auto">Masih belum ada data presensi yang masuk pada hari ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>

    </div>
</x-app-layout>
