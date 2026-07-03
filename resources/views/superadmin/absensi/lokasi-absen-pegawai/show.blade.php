@extends('layouts.app')

@section('content')
<div class="p-6">

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold">Override Lokasi Absen</h1>
            <div class="text-sm text-gray-600">
                Pegawai: <b>{{ $employee->name }}</b> ({{ $employee->nip ?? '-' }})
            </div>
        </div>

        <a href="{{ route('superadmin.absensi.lokasi-absen-pegawai.index') }}"
           class="bg-gray-200 text-gray-800 px-4 py-2 rounded">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.absensi.lokasi-absen-pegawai.store') }}"
          class="bg-white p-4 rounded shadow">
        @csrf

        <input type="hidden" name="employee_id" value="{{ $employee->id }}">

        <div class="mb-4 text-sm text-gray-600">
            Centang lokasi yang <b>diizinkan</b> untuk pegawai ini.
            Jika tidak ada yang dicentang, maka override akan <b>dikosongkan</b>.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($locations as $loc)
                <label class="flex items-start gap-3 border rounded p-3">
                    <input type="checkbox"
                           name="location_ids[]"
                           value="{{ $loc->id }}"
                           class="mt-1"
                           @checked(in_array($loc->id, $selected))>

                    <div>
                        <div class="font-semibold">{{ $loc->name }}</div>
                        <div class="text-xs text-gray-600">
                            {{ $loc->latitude }}, {{ $loc->longitude }} | Radius: {{ $loc->radius_meters }} m
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="mt-4 flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Simpan
            </button>

            <button type="submit"
                    name="location_ids"
                    value=""
                    class="bg-red-600 text-white px-4 py-2 rounded"
                    onclick="return confirm('Kosongkan semua override lokasi untuk pegawai ini?')">
                Kosongkan Override
            </button>
        </div>
    </form>

</div>
@endsection