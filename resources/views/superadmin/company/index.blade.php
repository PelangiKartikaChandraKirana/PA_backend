@extends('layouts.app')

@section('content')
<div class="px-6 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Instansi / OPD</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Kelola struktur organisasi dan unit kerja yang terdaftar di sistem.</p>
            </div>
        </div>

        <a href="{{ route('superadmin.master.instansi.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Tambah Instansi
        </a>
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
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Identitas Instansi</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Kode & Tipe</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Kontak</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Status</th>
                        <th class="px-6 py-4 text-right font-bold uppercase tracking-widest text-slate-500 text-[10px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($companies as $company)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-medium italic">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-sm">{{ $company->name }}</span>
                                    <span class="text-[10px] font-bold text-blue-500 mt-0.5 tracking-wider uppercase">{{ $company->short_name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <code class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded w-fit">{{ $company->kode_opd ?? '-' }}</code>
                                    <span class="text-[10px] font-bold text-slate-400 italic">Type: {{ $company->type ?? 'Umum' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col text-[11px] text-slate-600 gap-0.5">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $company->email ?? 'no-email' }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $company->phone ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($company->is_active ?? true)
                                    <span class="inline-flex rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-600 ring-1 ring-emerald-500/20">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-slate-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-slate-500 ring-1 ring-slate-400/20">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('superadmin.master.instansi.edit', $company->id) }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-100 transition hover:bg-amber-500 hover:text-white"
                                       title="Edit Instansi">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5M15.172 2.757a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682 10.682-10.682z"></path></svg>
                                    </a>

                                    <form action="{{ route('superadmin.master.instansi.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Hapus data instansi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 border border-rose-100 transition hover:bg-rose-500 hover:text-white"
                                            title="Hapus Instansi">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-500 italic">Belum ada data instansi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
            {{ $companies->links() }}
        </div>
        @endif
    </div>

</div>
@endsection