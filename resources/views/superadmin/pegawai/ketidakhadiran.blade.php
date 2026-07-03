@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dokumen Ketidakhadiran</h1>
            <p class="mt-1 text-sm text-gray-500">
               Menampung pengajuan dokumen dari Flutter, lalu superadmin dapat menyetujui atau menolak dokumen.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-3 text-center text-sm">
            <div class="rounded-xl bg-amber-50 px-4 py-3">
                <div class="text-xs uppercase tracking-wide text-amber-600">Pending</div>
                <div class="mt-1 text-xl font-bold text-amber-700">{{ $summary['pending'] }}</div>
            </div>
            <div class="rounded-xl bg-emerald-50 px-4 py-3">
                <div class="text-xs uppercase tracking-wide text-emerald-600">Approved</div>
                <div class="mt-1 text-xl font-bold text-emerald-700">{{ $summary['approved'] }}</div>
            </div>
            <div class="rounded-xl bg-rose-50 px-4 py-3">
                <div class="text-xs uppercase tracking-wide text-rose-600">Rejected</div>
                <div class="mt-1 text-xl font-bold text-rose-700">{{ $summary['rejected'] }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Pegawai</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Judul</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Periode</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">File</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($documents as $document)
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $document->employee->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $document->employee->nip ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $document->document_type }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $document->title }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ optional($document->start_date)->format('d M Y') }} - {{ optional($document->end_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($document->file_path)
                                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="font-medium text-blue-600 hover:underline">Lihat file</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusLabel = ucfirst($document->status);
                                    $statusClass = match ($document->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-rose-100 text-rose-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                                @if($document->approved_by)
                                    <div class="mt-1 text-xs text-gray-500">Disetujui oleh {{ $document->approved_by }}</div>
                                @endif
                                @if($document->rejected_by)
                                    <div class="mt-1 text-xs text-gray-500">Ditolak oleh {{ $document->rejected_by }}</div>
                                @endif
                                @if($document->decided_at)
                                    <div class="mt-1 text-xs text-gray-500">Diputuskan {{ $document->decided_at->format('d M Y H:i') }}</div>
                                @endif
                                @if($document->decision_notes)
                                    <div class="mt-1 rounded-lg bg-gray-50 px-2 py-1 text-xs text-gray-600">
                                        Catatan: {{ $document->decision_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('superadmin.pegawai.document.update', $document->id) }}" class="flex flex-col gap-2 md:flex-row md:items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        <option value="pending" @selected($document->status === 'pending')>Pending</option>
                                        <option value="approved" @selected($document->status === 'approved')>Approved</option>
                                        <option value="rejected" @selected($document->status === 'rejected')>Rejected</option>
                                    </select>
                                    <input
                                        type="text"
                                        name="decision_notes"
                                        value="{{ old('decision_notes', $document->decision_notes) }}"
                                        placeholder="Alasan/ catatan keputusan (wajib jika reject)"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm md:w-72"
                                    >
                                    <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                Belum ada dokumen ketidakhadiran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
