<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                    Data Pegawai
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    @if($unitKerja)
                        Unit Kerja/Instansi: <span class="font-medium text-slate-700">{{ $unitKerja }}</span>
                    @else
                        Semua Unit Kerja
                    @endif
                </p>
            </div>
            
            <div class="flex gap-2 w-full sm:w-auto">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search-input" placeholder="Cari NIP / Nama..." class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200/70 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block w-full outline-none transition-all duration-200">
                </div>
            </div>
        </div>

        <!-- TABLE DATA -->
        <div class="bg-white shadow-sm rounded-2xl border border-slate-200/60 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200/60 flex justify-between items-center bg-white">
                <h3 class="font-bold text-slate-800 text-lg">Daftar Pegawai</h3>
                <span class="text-xs px-3 py-1.5 bg-slate-50 border border-slate-200/60 text-slate-600 rounded-md font-medium tracking-wide">
                    {{ count($pegawai) }} Data
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="pegawai-table">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/60">
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">No</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Profil Pegawai</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Posisi & Jabatan</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Kontak</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Kadar Kepatuhan</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Status</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pegawai as $index => $user)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-200 group searchable-row">
                                <td class="px-6 py-4 text-sm text-slate-500 font-medium whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($user->employee && $user->employee->photo)
                                            <img src="{{ Storage::url($user->employee->photo) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200/60 shadow-sm">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm ring-1 ring-slate-200/60">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 searchable-name">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5 searchable-nip">
                                                NIP. {{ $user->nip ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-700">
                                            {{ $user->employee?->position?->name ?? 'Belum ada jabatan' }}
                                        </span>
                                        <span class="text-xs text-slate-400 mt-0.5">
                                            {{ $user->employee?->department?->name ?? 'Belum ada bidang/divisi' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-sm text-slate-600">
                                        <div class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200/60 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="truncate max-w-[150px] font-medium" title="{{ $user->email }}">{{ $user->email ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kepatuhan</span>
                                            <span class="text-xs font-bold {{ $user->compliance_score > 80 ? 'text-emerald-600' : ($user->compliance_score > 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                                {{ $user->compliance_score }}%
                                            </span>
                                        </div>
                                        <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $user->compliance_score > 80 ? 'bg-emerald-500' : ($user->compliance_score > 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $user->compliance_score }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($user->status === 'aktif' || strtolower($user->status) === 'active')
                                    <a href="{{ route('admin.pegawai.show', $user->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 flex items-center justify-center rounded-2xl mb-4 border border-slate-200/60">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <h3 class="text-slate-800 font-bold mb-1">Data Pegawai Kosong</h3>
                                        <p class="text-slate-500 text-sm max-w-sm mx-auto">Tidak ada pegawai yang terdaftar pada instansi Anda saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- SIMPLE SEARCH SCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("search-input");
            const rows = document.querySelectorAll(".searchable-row");

            if (searchInput) {
                searchInput.addEventListener("input", function(e) {
                    const term = e.target.value.toLowerCase();

                    rows.forEach(row => {
                        const name = row.querySelector(".searchable-name")?.textContent.toLowerCase() || "";
                        const nip = row.querySelector(".searchable-nip")?.textContent.toLowerCase() || "";

                        if (name.includes(term) || nip.includes(term)) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
