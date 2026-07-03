@extends('layouts.app')

@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Edit Lokasi Absen Instansi</h1>

    @if($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.absensi.lokasi-absen-instansi.update', $item->id) }}" method="POST"
          class="bg-white p-6 rounded-lg shadow space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium">Nama Lokasi</label>
            <input name="name" value="{{ old('name', $item->name) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">Latitude</label>
                <input name="latitude" value="{{ old('latitude', $item->latitude) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Longitude</label>
                <input name="longitude" value="{{ old('longitude', $item->longitude) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">Radius (meter)</label>
                <input type="number" min="1" name="radius" value="{{ old('radius', $item->radius) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">
                    Instansi
                </label>

                <select
                    name="company_id"
                    class="w-full border rounded px-3 py-2"
                    required>

                    @foreach($companies as $company)
                        <option
                            value="{{ $company->id }}"
                            {{ old('company_id', $item->companyLocation->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach

                </select>
            </div>
        </div>

        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
            <span class="text-sm">Aktif</span>
        </label>

        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            <a href="{{ route('superadmin.absensi.lokasi-absen-instansi.index') }}"
               class="bg-gray-200 px-4 py-2 rounded">Batal</a>
        </div>
    </form>
</div>
@endsection