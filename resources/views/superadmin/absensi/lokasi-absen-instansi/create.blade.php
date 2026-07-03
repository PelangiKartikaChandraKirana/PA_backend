@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.absensi.lokasi-absen-instansi.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Lokasi Instansi</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pilih titik koordinat lalu kaitkan ke instansi / unit kerja.</p>
        </div>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <form action="{{ route('superadmin.absensi.lokasi-absen-instansi.store') }}" method="POST">
        @csrf
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            {{-- Section: Pilih Lokasi --}}
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Pilih Titik Lokasi</h2>
            </div>
            <div class="px-6 py-5 space-y-4">

                @if($locations->isEmpty())
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-4 text-sm text-amber-700 flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Belum ada titik lokasi koordinat yang terdaftar.</p>
                            <p class="mt-0.5">Silakan tambahkan lokasi terlebih dahulu di menu
                                <a href="{{ route('superadmin.absensi.lokasi-absen.index') }}" class="font-medium underline hover:text-amber-900">Lokasi Absen</a>.
                            </p>
                        </div>
                    </div>
                @else
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Lokasi Koordinat <span class="text-red-500">*</span>
                        </label>
                        <select name="location_id" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="">- Pilih Lokasi -</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} — {{ $loc->latitude }}, {{ $loc->longitude }} ({{ $loc->radius_meters }}m)
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-400">
                            Koordinat dan radius mengikuti data lokasi yang sudah terdaftar.
                            <a href="{{ route('superadmin.absensi.lokasi-absen.index') }}" class="text-blue-500 hover:underline">Kelola Lokasi →</a>
                        </p>
                    </div>
                @endif

            </div>

            {{-- Section: Info Unit Kerja --}}
            <div class="border-y border-gray-200 px-6 py-4 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Informasi Unit Kerja</h2>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Instansi <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="company_id"
                        required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                        <option value="">-- Pilih Instansi --</option>

                        @foreach($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach

                    </select>

                    @error('company_id')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="flex items-center gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
                <a href="{{ route('superadmin.absensi.lokasi-absen-instansi.index') }}"
                   class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </div>
    </form>

</div>
@endsection