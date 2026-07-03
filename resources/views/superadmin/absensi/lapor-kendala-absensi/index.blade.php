@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Pusat Bantuan & Kendala Absensi</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola dan pantau seluruh laporan gangguan teknis absensi dari seluruh unit kerja.</p>
        </div>
        <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-95">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        {{-- Total Tickets --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-slate-50 p-3 text-slate-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Laporan</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ number_format($stats['total']) }} <span class="text-xs font-medium text-gray-400 font-normal">Tiket</span></h3>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="rounded-2xl border border-amber-100 bg-amber-50/30 p-5 shadow-sm ring-1 ring-amber-100 transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-amber-500 p-3 text-white shadow-lg shadow-amber-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Menunggu Respon</p>
                    <h3 class="text-2xl font-black text-amber-700">{{ number_format($stats['pending']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Resolved --}}
        <div class="rounded-2xl border border-green-100 bg-green-50/30 p-5 shadow-sm ring-1 ring-green-100 transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-green-500 p-3 text-white shadow-lg shadow-green-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-green-600">Terselesaikan</p>
                    <h3 class="text-2xl font-black text-green-700">{{ number_format($stats['resolved']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <form method="GET" class="grid grid-cols-1 gap-4 p-5 md:grid-cols-12 md:items-end">
            <div class="md:col-span-5">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Cari Keterangan Kendala</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Ketik deskripsi masalah..."
                           class="w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all bg-gray-50/30">
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Kategori Masalah</label>
                <select name="type_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 appearance-none bg-white">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" @selected((string)$typeId===(string)$t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Status Tiket</label>
                <select name="status_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 appearance-none bg-white">
                    <option value="">-- Semua Status --</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}" @selected((string)$statusId===(string)$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-black active:scale-95 transition-all">
                    Filter
                </button>
                <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-400 hover:bg-gray-50 active:scale-95 transition-all" title="Reset">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Main Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50/80 font-bold text-gray-500 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4 text-left w-16">No</th>
                        <th class="px-6 py-4 text-left">Informasi Kendala</th>
                        <th class="px-6 py-4 text-left">Pelapor / Lokasi</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Bukti</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 font-mono text-gray-400">{{ $loop->iteration + ($items->currentPage()-1)*$items->perPage() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-indigo-700 border border-indigo-100 w-fit mb-1.5">
                                        {{ $item->machineFaultType->name ?? 'Lain-lain' }}
                                    </span>
                                    <div class="font-bold text-gray-800 tracking-tight leading-snug line-clamp-2 max-w-xs" title="{{ $item->description }}">
                                        {{ $item->description ?? 'Tidak ada deskripsi' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-1.5">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ optional($item->incident_date)->format('d M Y') ?? '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->employee)
                                    <div class="text-[12px] font-bold text-gray-700 tracking-tight leading-tight">{{ $item->employee->name }}</div>
                                    <div class="text-[10px] text-blue-500 font-bold uppercase mt-0.5">{{ $item->employee->department->name ?? 'Unit Tidak Diketahui' }}</div>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Laporan Admin</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusName = strtolower($item->machineFaultStatus->name ?? '');
                                    $badgeColor = 'bg-gray-100 text-gray-600 border-gray-200';
                                    if(str_contains($statusName, 'pending') || str_contains($statusName, 'baru')) $badgeColor = 'bg-red-50 text-red-700 border-red-200 ring-4 ring-red-50/20';
                                    elseif(str_contains($statusName, 'proses')) $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200 ring-4 ring-amber-50/20';
                                    elseif(str_contains($statusName, 'selesai') || str_contains($statusName, 'resolved')) $badgeColor = 'bg-green-50 text-green-700 border-green-200';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-tight border shadow-sm {{ $badgeColor }}">
                                    @if(str_contains($statusName, 'proses'))
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @endif
                                    {{ $item->machineFaultStatus->name ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->evidence_path)
                                    <button type="button" onclick="openEvidence('{{ asset('storage/'.$item->evidence_path) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-300 transition-all active:scale-95 group">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </button>
                                @else
                                    <span class="text-[11px] text-gray-300 italic">No Bukti</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.absensi.lapor-kendala-absensi.edit', $item->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-200 px-3 py-1.5 text-[11px] font-bold text-gray-600 hover:border-amber-300 hover:text-amber-600 shadow-sm transition-all active:scale-95 group">
                                        Ganti Status / Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.absensi.lapor-kendala-absensi.destroy', $item->id) }}"
                                          onsubmit="return confirm('Hapus tiket kendala ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg bg-red-50 border border-red-50 px-2 py-1.5 text-red-400 hover:bg-red-600 hover:text-white transition-all active:scale-95">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-gray-400">
                                    <div class="rounded-full bg-slate-50 p-6 border border-slate-100">
                                        <svg class="h-12 w-12 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="max-w-xs mx-auto">
                                        <h4 class="text-sm font-bold text-gray-800">Semua Terkendali</h4>
                                        <p class="text-xs text-gray-500 mt-1 italic">Saat ini belum ada laporan kendala teknis yang masuk ke sistem.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Lightbox Modal --}}
<div id="evidenceModal" class="fixed inset-0 z-50 hidden bg-slate-900/95 backdrop-blur-sm flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
    <div class="relative max-w-4xl w-full animate-in zoom-in duration-300" onclick="event.stopPropagation()">
        <img id="evidenceImage" src="" alt="Bukti Kendala" class="w-full h-auto rounded-2xl shadow-2xl border-4 border-white/20">
        <button onclick="document.getElementById('evidenceModal').classList.add('hidden')" class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-xl hover:bg-gray-100 transition">
            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

<script>
    function openEvidence(src) {
        document.getElementById('evidenceImage').src = src;
        document.getElementById('evidenceModal').classList.remove('hidden');
    }
</script>
@endsection