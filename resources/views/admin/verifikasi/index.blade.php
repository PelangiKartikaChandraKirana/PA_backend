<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Verifikasi Ketidakhadiran</h2>
                <p class="text-sm text-slate-500 mt-1">Daftar pengajuan izin, sakit, dan cuti pegawai unit <span class="font-semibold text-blue-600">{{ $unitKerja }}</span></p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- LIST PENDING -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($documents as $doc)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <!-- INFO PEGAWAI -->
                            <div class="lg:w-1/4">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                        {{ strtoupper(substr($doc->employee->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $doc->employee->name }}</h4>
                                        <p class="text-xs text-slate-500">NIP. {{ $doc->employee->nip }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-xs text-slate-600">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $doc->start_date->format('d M Y') }} - {{ $doc->end_date->format('d M Y') }}
                                    </div>
                                    <div class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wider rounded-md border border-amber-100 inline-block">
                                        Menunggu Persetujuan
                                    </div>
                                </div>
                            </div>

                            <!-- DETAIL PENGALAMAN -->
                            <div class="lg:w-1/2 flex-1">
                                <div class="mb-4">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg mb-2 inline-block">{{ $doc->document_type }}</span>
                                    <h3 class="text-lg font-bold text-slate-800">{{ $doc->title }}</h3>
                                    <p class="text-sm text-slate-600 mt-2 italic">"{{ $doc->notes ?? 'Tidak ada catatan tambahan.' }}"</p>
                                </div>

                                @if($doc->file_path)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 text-sm font-semibold rounded-xl border border-blue-100 hover:bg-blue-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        Lihat Lampiran Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-medium italic">Tidak ada lampiran file.</span>
                                @endif
                            </div>

                            <!-- AKSI -->
                            <div class="lg:w-1/4 flex flex-col justify-center gap-3">
                                <form action="{{ route('admin.verifikasi.izin.approve', $doc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-sm shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Setujui Izin
                                    </button>
                                </form>
                                <button type="button" @click="$dispatch('open-modal', 'reject-modal-{{ $doc->id }}')" class="w-full px-6 py-3 bg-white text-rose-600 border border-rose-200 font-bold rounded-xl hover:bg-rose-50 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak Pengajuan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL TOLAK -->
                <x-modal name="reject-modal-{{ $doc->id }}" focusable>
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-slate-800">Tolak Pengajuan - {{ $doc->employee->name }}</h2>
                        <p class="mt-1 text-sm text-slate-600 italic">Berikan alasan penolakan agar pegawai mengetahui penyebabnya.</p>
                        
                        <form action="{{ route('admin.verifikasi.izin.reject', $doc->id) }}" method="POST" class="mt-6">
                            @csrf
                            <div>
                                <x-input-label for="notes" value="Alasan Penolakan" class="sr-only" />
                                <textarea name="notes" placeholder="Contoh: Lampiran tidak jelas / Tanggal tidak sesuai..." class="w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required></textarea>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-lg hover:bg-rose-700 transition-all">Konfirmasi Tolak</button>
                            </div>
                        </form>
                    </div>
                </x-modal>
            @empty
                <div class="bg-white p-20 rounded-2xl border border-slate-200/60 flex flex-col items-center justify-center text-center shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6 border border-slate-100">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Tidak Ada Pengajuan Pending</h3>
                    <p class="text-slate-500 mt-2 max-w-sm">Semua berkas izin dan sakit dari unit Anda telah diproses atau belum ada pengajuan baru.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
