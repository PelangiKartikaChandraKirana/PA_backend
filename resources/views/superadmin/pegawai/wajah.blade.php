@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Wajah Pegawai</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola dan sinkronisasi data wajah pegawai untuk presensi.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('superadmin.pegawai.wajah') }}" method="GET" class="relative w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama atau NIP..." 
                       class="w-full sm:w-64 rounded-xl border border-slate-300 pl-10 pr-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                <svg class="absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>

            <form method="POST" action="{{ route('superadmin.pegawai.wajah.sync-all') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        onclick="return confirm('Sync semua wajah aktif ke face service?')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Sync Semua Wajah
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-6 rounded-xl bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Pegawai</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Upload Baru</th>
                        <th class="border-b border-slate-200 px-6 py-4 font-semibold">Data Wajah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($employees as $emp)
                    <tr class="align-top hover:bg-slate-50/50 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $emp->name }}</div>
                            <div class="text-xs text-slate-500 mt-1">NIP: {{ $emp->nip ?? '-' }}</div>
                            <div class="mt-2 text-xs">
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-1 text-blue-700 font-medium border border-blue-200">
                                    Wajah Terdaftar: {{ $emp->faces->count() }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <form method="POST"
                                  action="{{ route('superadmin.pegawai.wajah.store', $emp->id) }}"
                                  enctype="multipart/form-data" 
                                  class="flex flex-col gap-2">
                                @csrf
                                <input type="file" name="face_image" required
                                       class="block w-full text-xs text-slate-500
                                              file:mr-2 file:py-1.5 file:px-3
                                              file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100">
                                <button class="self-start inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 text-xs font-medium rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Upload & Sync
                                </button>
                            </form>
                        </td>

                        <td class="px-6 py-4">
                            @if($emp->faces->count() > 0)
                                <div class="flex flex-wrap gap-3">
                                @foreach($emp->faces as $face)
                                    <div class="relative flex w-28 flex-col items-center rounded-xl border border-slate-200 bg-white p-2 shadow-sm transition hover:shadow-md">
                                        <!-- Foto Thumbnail -->
                                        <div class="relative h-16 w-16 mb-2 rounded-full overflow-hidden {{ $face->is_active ? 'ring-2 ring-green-500 ring-offset-2' : 'grayscale opacity-60' }}">
                                            <img src="{{ asset('storage/' . $face->image_path) }}" alt="Face" class="h-full w-full object-cover text-[8px]" onerror="this.onerror=null; this.outerHTML='<svg class=\'w-full h-full text-slate-300 bg-slate-100 p-2\' fill=\'currentColor\' viewBox=\'0 0 24 24\'><path d=\'M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z\' /></svg>'">
                                        </div>
                                        
                                        <!-- Status Badge -->
                                        @if($face->is_active)
                                            <span class="mb-2 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-700 border border-green-200">Aktif</span>
                                        @else
                                            <span class="mb-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600 border border-slate-200">Nonaktif</span>
                                        @endif

                                        <!-- Aksi Buttons -->
                                        <div class="flex w-full justify-center gap-1 border-t border-slate-100 pt-2">
                                            @if(!$face->is_active)
                                                <form method="POST" action="{{ route('superadmin.pegawai.wajah.aktif', $face->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" title="Jadikan Aktif" class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-green-50 text-green-600 transition hover:bg-green-100 hover:text-green-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form method="POST" action="{{ route('superadmin.pegawai.wajah.delete', $face->id) }}" onsubmit="return confirm('Hapus data wajah ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Wajah" class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-red-50 text-red-600 transition hover:bg-red-100 hover:text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            @else
                                <div class="flex h-16 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-xs italic text-slate-400">
                                    Belum ada data wajah
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">
                            Tidak ada data pegawai yang terdaftar.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection