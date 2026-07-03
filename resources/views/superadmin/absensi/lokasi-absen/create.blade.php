@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.absensi.lokasi-absen.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Lokasi Absen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Klik pada peta untuk menentukan titik koordinat kantor/unit kerja.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.absensi.lokasi-absen.store') }}">
        @csrf
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            {{-- Peta Interaktif --}}
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">📍 Titik Koordinat (Klik Peta)</h2>
            </div>
            <div class="p-4">
                <div class="w-full overflow-hidden rounded-xl border border-gray-300 shadow-inner" style="height:340px;">
                    <div id="map" style="height:100%;width:100%;"></div>
                </div>
                <p class="mt-2 text-xs text-gray-400">Klik titik mana saja di peta. Koordinat akan terisi otomatis. Anda juga dapat menggeser pin setelah menjatuhkannya.</p>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Latitude <span class="text-red-500">*</span></label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                               readonly placeholder="Otomatis dari klik peta"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 font-mono text-sm shadow-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Longitude <span class="text-red-500">*</span></label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
                               readonly placeholder="Otomatis dari klik peta"
                               class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 font-mono text-sm shadow-sm cursor-not-allowed">
                    </div>
                </div>
            </div>

            {{-- Info Lokasi --}}
            <div class="border-y border-gray-200 px-6 py-4 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Informasi Lokasi</h2>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Contoh: Kantor Bupati Lamongan"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                           required>
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Radius Absen <span class="text-red-500">*</span>
                        <span class="ml-1 text-xs font-normal text-gray-400">(dalam meter)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="radius_meters" id="radiusInput"
                               value="{{ old('radius_meters', 100) }}"
                               min="10" max="5000" step="10"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                               oninput="updateRadiusCircle(this.value)"
                               required>
                        <span class="text-sm text-gray-500 whitespace-nowrap">meter</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Pegawai harus berada dalam jarak ini dari titik koordinat agar absensi diterima.</p>
                    @error('radius_meters')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">OPD / Unit Kerja <span class="text-gray-400 text-xs">(opsional)</span></label>
                    <select name="unit_id" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Tidak ditentukan --</option>
                        @foreach($units ?? [] as $u)
                            <option value="{{ $u->id }}" @selected(old('unit_id') == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true))
                           class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Lokasi ini aktif dan bisa digunakan untuk absensi</span>
                </label>

            </div>

            {{-- Footer --}}
            <div class="flex items-center gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Lokasi
                </button>
                <a href="{{ route('superadmin.absensi.lokasi-absen.index') }}"
                   class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Leaflet.js Maps --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Default center: Indonesia
    const defaultLat = {{ old('latitude', -7.2575) }};
    const defaultLng = {{ old('longitude', 112.7521) }};

    const map = L.map('map').setView([defaultLat, defaultLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;
    let circle = null;

    function updateRadiusCircle(radius) {
        if (circle) { map.removeLayer(circle); }
        if (marker) {
            const latlng = marker.getLatLng();
            circle = L.circle(latlng, {
                radius: parseInt(radius),
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.15,
                weight: 2,
            }).addTo(map);
        }
    }

    function setMarker(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);

        if (marker) { map.removeLayer(marker); }
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            setMarker(pos.lat, pos.lng);
        });

        const radius = parseInt(document.getElementById('radiusInput').value) || 100;
        updateRadiusCircle(radius);
    }

    // If old values exist (form error), restore marker
    @if(old('latitude') && old('longitude'))
        setMarker({{ old('latitude') }}, {{ old('longitude') }});
    @endif

    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });
</script>
@endsection