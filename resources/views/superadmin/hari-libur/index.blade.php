@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Hari Libur</h1>
            <p class="mt-1 text-sm text-slate-500">Daftar hari libur nasional, instansi, dan hari besar lainnya.</p>
        </div>

        <a href="{{ route('superadmin.hari-libur.create') }}"
           class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
            Tambah Hari Libur
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-700">Data Hari Libur</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Nasional</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Berulang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Tahun</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-slate-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($holidays as $holiday)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $holiday->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ \Carbon\Carbon::parse($holiday->date)->format('d-m-Y') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $holiday->type }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($holiday->is_nasional)
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">Ya</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">Tidak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($holiday->is_recurring)
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">Ya</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">Tidak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $holiday->year ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('superadmin.hari-libur.edit', $holiday->id) }}"
                                       class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-amber-600">
                                        Edit
                                    </a>

                                    <form action="{{ route('superadmin.hari-libur.destroy', $holiday->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus hari libur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada data hari libur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection