@extends('layouts.app')

@section('content')
<div class="px-6 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Tipe Dokumen</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Konfigurasi jenis dokumen perizinan dan ketidakhadiran pegawai.</p>
            </div>
        </div>

        <a href="{{ route('superadmin.master.tipe-dokumen.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Tambah Tipe Dokumen
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
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
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Tipe Dokumen</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-widest text-slate-500 text-[10px]">Visual</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Ketentuan</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-widest text-slate-500 text-[10px]">Status</th>
                        <th class="px-6 py-4 text-right font-bold uppercase tracking-widest text-slate-500 text-[10px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($items ?? [] as $item)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-medium italic">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-sm">{{ $item->name }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <code class="text-[10px] font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded uppercase ring-1 ring-slate-200/50">{{ $item->code }}</code>
                                        <span class="text-[10px] font-bold text-indigo-500 tracking-tighter uppercase">{{ $item->category }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="w-6 h-6 rounded-full mx-auto shadow-sm ring-2 ring-white ring-offset-2 ring-offset-slate-100" style="background-color: {{ $item->color ?? '#3b82f6' }}"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    @if($item->requires_approval)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-blue-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            BUTUH APPROVAL
                                        </span>
                                    @endif
                                    @if($item->is_required)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            LAMPIRAN WAJIB
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->is_active)
                                    <span class="inline-flex rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-600 ring-1 ring-emerald-500/20">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-rose-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-rose-600 ring-1 ring-rose-500/20">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('superadmin.master.tipe-dokumen.edit', $item->id) }}"
                                       class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-100 transition hover:bg-amber-500 hover:text-white">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5M15.172 2.757a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682 10.682-10.682z"></path></svg>
                                    </a>

                                    <form action="{{ route('superadmin.master.tipe-dokumen.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus tipe dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 border border-rose-100 transition hover:bg-rose-500 hover:text-white">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-500">Belum ada data tipe dokumen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection