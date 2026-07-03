<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Verifikasi Kendala Mesin</h2>
                <p class="text-sm text-slate-500 mt-1">Daftar laporan kendala presensi dari pegawai unit <span class="font-semibold text-orange-600">{{ $unitKerja }}</span></p>
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($reports as $report)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col h-full border-l-4 border-l-orange-500 hover:shadow-md transition-all">
                    <div class="p-6 flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($report->employee->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $report->employee->name }}</h4>
                                <p class="text-[10px] text-slate-500 font-medium">Laporan Tgl: {{ $report->report_date->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-base font-bold text-slate-800">{{ $report->title }}</h3>
                            <p class="text-sm text-slate-500 mt-2 line-clamp-3 italic">"{{ $report->description ?? 'Tidak ada deskripsi.' }}"</p>
                        </div>

                        @if($report->evidence_path)
                            <div class="mb-4">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Foto Bukti Kendala</p>
                                <img src="{{ asset('storage/' . $report->evidence_path) }}" class="w-full h-40 object-cover rounded-xl border border-slate-100 cursor-pointer" @click="$dispatch('open-modal', 'image-preview-{{ $report->id }}')">
                            </div>

                            <!-- MODAL PREVIEW IMAGE -->
                            <x-modal name="image-preview-{{ $report->id }}" max-width="2xl">
                                <div class="p-4 bg-slate-950">
                                    <img src="{{ asset('storage/' . $report->evidence_path) }}" class="w-full h-auto rounded-lg">
                                    <div class="mt-4 flex justify-end">
                                        <button @click="$dispatch('close')" class="text-white text-sm font-bold px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-all">Tutup</button>
                                    </div>
                                </div>
                            </x-modal>
                        @endif
                    </div>

                    <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex gap-2">
                        <form action="{{ route('admin.verifikasi.kendala.approve', $report->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition-all flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Validasi
                            </button>
                        </form>
                        <form action="{{ route('admin.verifikasi.kendala.reject', $report->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-white text-slate-600 border border-slate-200 text-xs font-bold rounded-lg hover:bg-slate-50 transition-all flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-20 rounded-2xl border border-slate-200/60 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Tidak Ada Laporan Kendala</h3>
                    <p class="text-slate-500 mt-2 max-w-sm">Semua sistem presensi di unit Anda berjalan lancar atau laporan telah diproses.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
