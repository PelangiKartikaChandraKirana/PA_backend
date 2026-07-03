<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.pegawai') }}" class="p-2 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition-all text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Profil Pegawai</h2>
                    <p class="text-sm text-slate-500 mt-1">Informasi lengkap dan riwayat aktivitas pegawai</p>
                </div>
            </div>
            <div class="flex gap-2">
                @if($user->status === 'aktif')
                    <span class="px-4 py-2 bg-emerald-50 text-emerald-600 text-sm font-bold rounded-xl border border-emerald-100">Aktif</span>
                @else
                    <span class="px-4 py-2 bg-slate-50 text-slate-500 text-sm font-bold rounded-xl border border-slate-200">Tidak Aktif</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- SIDEBAR: PROFIL RINGKAS -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 text-center">
                    <div class="relative inline-block mb-4">
                        @if($user->employee && $user->employee->photo)
                            <img src="{{ Storage::url($user->employee->photo) }}" class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-lg mx-auto">
                        @else
                            <div class="w-32 h-32 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-4xl font-bold border-4 border-white shadow-lg mx-auto">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">{{ $user->name }}</h3>
                    <p class="text-sm text-slate-500 font-medium mt-1">NIP. {{ $user->nip ?? '-' }}</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Unit Kerja</span>
                            <span class="font-semibold text-slate-700">{{ $user->unit_kerja }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Jabatan</span>
                            <span class="font-semibold text-slate-700">{{ $user->employee?->position?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-slate-800 text-sm">Kadar Kepatuhan</h4>
                        <span class="text-xs font-bold {{ $user->compliance_score > 80 ? 'text-emerald-600' : ($user->compliance_score > 50 ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ $user->compliance_score }}%
                        </span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-2">
                        <div class="h-full {{ $user->compliance_score > 80 ? 'bg-emerald-500' : ($user->compliance_score > 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $user->compliance_score }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Berdasarkan tingkat kehadiran dalam 30 hari terakhir.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Informasi Kontak
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</p>
                            <p class="text-sm text-slate-700 font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Username</p>
                            <p class="text-sm text-slate-700 font-medium">{{ $user->username ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT: RIWAYAT -->
            <div class="lg:col-span-2 space-y-6">
                <!-- TAB HEADERS (Simple) -->
                <div class="flex gap-4 border-b border-slate-200">
                    <button class="px-4 py-2 border-b-2 border-blue-600 text-blue-600 font-bold text-sm">Aktivitas Terakhir</button>
                    <!-- More tabs can be added here -->
                </div>

                <!-- PRESENSI TERBARU -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h4 class="font-bold text-slate-800 text-sm">Presensi Terbaru</h4>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">10 Log Terakhir</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold tracking-widest">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Jam Masuk</th>
                                    <th class="px-6 py-3">Jam Pulang</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($user->employee->attendanceLogs ?? [] as $log)
                                    <tr class="text-sm hover:bg-slate-50/50 transition-all">
                                        <td class="px-6 py-4 font-medium text-slate-700">{{ $log->attendance_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-slate-600 font-mono">{{ $log->check_in ?? '--:--' }}</td>
                                        <td class="px-6 py-4 text-slate-600 font-mono">{{ $log->check_out ?? '--:--' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->status === 'hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Belum ada data presensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PENGAJUAN DOKUMEN -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h4 class="font-bold text-slate-800 text-sm">Pengajuan Izin/Sakit</h4>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">5 Terakhir</span>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($user->employee->absenceDocuments ?? [] as $doc)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $doc->title }}</p>
                                        <p class="text-xs text-slate-500">{{ $doc->document_type }} • {{ $doc->start_date->format('d M') }} - {{ $doc->end_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $statusClass = match($doc->status) {
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase {{ $statusClass }}">
                                        {{ $doc->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-400 text-sm italic py-4">Belum ada pengajuan dokumen.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
