@extends('layouts.app')

@section('content')
<div class="px-6 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Data TPP</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Konfigurasi besaran dasar Tambahan Penghasilan Pegawai untuk stimulasi kinerja.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm animate-in fade-in slide-in-from-top-4">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Section -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">No</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Identitas Pegawai</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Jabatan</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Besaran TPP Dasar (Rp)</th>
                        <th class="px-6 py-4 text-right font-bold uppercase tracking-widest text-slate-500 text-[10px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employees as $employee)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <form action="{{ route('superadmin.master.tpp.update', $employee->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td class="px-6 py-4 text-slate-400 font-medium italic">{{ $loop->iteration + $employees->firstItem() - 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-sm">{{ $employee->name }}</span>
                                        <span class="text-xs font-semibold text-slate-400 mt-0.5">NIP. {{ $employee->nip }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-600 font-medium">{{ $employee->position->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative max-w-[200px]">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400">Rp</span>
                                        <input type="number" name="tpp_allowance" 
                                               value="{{ $employee->tpp_allowance ?? 5000000 }}"
                                               class="w-full rounded-xl border-slate-200 pl-8 pr-4 py-2 text-sm font-bold text-slate-700 bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition shadow-sm outline-none" required>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Update
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-500">Belum ada data pegawai untuk dikonfigurasi TPP.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
