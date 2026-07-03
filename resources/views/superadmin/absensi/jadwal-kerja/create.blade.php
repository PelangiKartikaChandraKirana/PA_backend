@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Tambah Jadwal Kerja</h1>
        <p class="mt-1 text-sm text-slate-500">Atur jadwal masuk, pulang, dan aturan absensi dengan tampilan bersih dan modern.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="font-semibold mb-1">Terjadi kesalahan:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-4xl">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-700">Ketentuan Jam Absensi ASN</h2>
                        <p class="mt-1 text-sm text-slate-500">Lengkapi aturan absensi ASN sesuai panduan.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('superadmin.absensi.jadwal-kerja.store') }}" method="POST" class="p-6">
                @csrf

                @if($categories->count())
                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">
                                Kategori Jadwal <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="category_id"
                                id="category_id"
                                required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('category_id') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                            >
                                <option value="">Pilih kategori jadwal</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) old('category_id', $categoryId) === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                        <p class="font-semibold">Perhatian:</p>
                        <p class="mt-2">Tidak ada kategori jadwal kerja. Silakan buat kategori baru terlebih dahulu di menu Kategori Jadwal Kerja sebelum menambah jadwal absensi.</p>
                    </div>
                @endif

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 mb-6">
                    <p class="text-sm text-slate-700 font-semibold">Ketentuan Jam Absensi ASN</p>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-slate-500">
                        <li>Hari Senin s.d. Kamis: <span class="font-semibold text-slate-700">07:00 - 15:30</span>.</li>
                        <li>Hari Jumat: <span class="font-semibold text-slate-700">07:00 - 15:00</span>.</li>
                        <li>Toleransi keterlambatan <span class="font-semibold text-slate-700">0 menit</span> (keterlambatan dihitung sejak 07:31).</li>
                        <li>Wajib mengikuti <span class="font-semibold text-slate-700">Apel Pagi</span> setiap hari Senin.</li>
                        <li>Biarkan tipe pegawai kosong bila aturan berlaku untuk semua ASN.</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 mb-6">
                    <p class="text-sm text-slate-700 font-semibold">Contoh Pengisian</p>
                    <div class="mt-3 space-y-3 text-sm text-slate-500">
                        <div class="rounded-2xl bg-slate-100 p-3">
                            <p class="font-medium text-slate-700">Contoh 1: Jadwal ASN Senin - Kamis</p>
                            <p>Senin - Kamis | Jam Masuk: 07:00 | Jam Pulang: 15:30 | Toleransi: 0 menit</p>
                        </div>
                        <div class="rounded-2xl bg-slate-100 p-3">
                            <p class="font-medium text-slate-700">Contoh 2: Jadwal ASN Jumat</p>
                            <p>Jumat | Jam Masuk: 07:00 | Jam Pulang: 15:00 | Toleransi: 0 menit</p>
                        </div>
                        <p class="text-slate-500">
                            Format jam harus <span class="font-semibold">HH:MM</span>. Kosongkan tipe pegawai jika jadwal berlaku untuk seluruh ASN.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">Jam Absensi ASN</p>
                                    <p class="text-sm text-slate-500">Tentukan jam masuk dan pulang untuk jadwal kerja ASN.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mt-4">
                                <div class="md:col-span-1">
                                    <label for="day_of_week" class="mb-2 block text-sm font-medium text-slate-700">
                                        Hari Kerja <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        name="day_of_week"
                                        id="day_of_week"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('day_of_week') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                        required>
                                        <option value="">Pilih Hari</option>
                                        <option value="1" {{ old('day_of_week') == 1 ? 'selected' : '' }}>Senin</option>
                                        <option value="2" {{ old('day_of_week') == 2 ? 'selected' : '' }}>Selasa</option>
                                        <option value="3" {{ old('day_of_week') == 3 ? 'selected' : '' }}>Rabu</option>
                                        <option value="4" {{ old('day_of_week') == 4 ? 'selected' : '' }}>Kamis</option>
                                        <option value="5" {{ old('day_of_week') == 5 ? 'selected' : '' }}>Jumat</option>
                                        <option value="6" {{ old('day_of_week') == 6 ? 'selected' : '' }}>Sabtu</option>
                                        <option value="7" {{ old('day_of_week') == 7 ? 'selected' : '' }}>Minggu</option>
                                    </select>
                                    @error('day_of_week')
                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-1">
                                    <label for="start_time" class="mb-2 block text-sm font-medium text-slate-700">
                                        Jam Masuk <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="time"
                                        name="start_time"
                                        id="start_time"
                                        value="{{ old('start_time') }}"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('start_time') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                        required
                                    >
                                    @error('start_time')
                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-1">
                                    <label for="end_time" class="mb-2 block text-sm font-medium text-slate-700">
                                        Jam Pulang <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="time"
                                        name="end_time"
                                        id="end_time"
                                        value="{{ old('end_time') }}"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('end_time') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                        required
                                    >
                                    @error('end_time')
                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">Ketentuan Tambahan</p>
                                    <p class="text-sm text-slate-500">Atur toleransi dan menit kerja untuk perhitungan absensi.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mt-4">
                                <div>
                                    <label for="tolerance_minutes" class="mb-2 block text-sm font-medium text-slate-700">
                                        Toleransi Keterlambatan
                                    </label>
                                    <input
                                        type="number"
                                        name="tolerance_minutes"
                                        id="tolerance_minutes"
                                        value="{{ old('tolerance_minutes', 15) }}"
                                        placeholder="15 menit"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('tolerance_minutes') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                        min="0"
                                    >
                                    @error('tolerance_minutes')
                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="effective_minutes" class="mb-2 block text-sm font-medium text-slate-700">
                                        Menit Efektif Kerja
                                    </label>
                                    <input
                                        type="number"
                                        name="effective_minutes"
                                        id="effective_minutes"
                                        value="{{ old('effective_minutes', 450) }}"
                                        placeholder="450 menit"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('effective_minutes') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                        min="0"
                                    >
                                    @error('effective_minutes')
                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="employee_type" class="mb-2 block text-sm font-medium text-slate-700">
                                        Tipe Pegawai (opsional)
                                    </label>
                                    <input
                                        type="text"
                                        name="employee_type"
                                        id="employee_type"
                                        value="{{ old('employee_type') }}"
                                        placeholder="Contoh: ASN / Honorer / PPPK"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('employee_type') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                    >
                                    @error('employee_type')
                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            id="is_active"
                                            value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}
                                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="text-sm font-medium text-slate-700">Aktifkan Jadwal</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    @if($categories->count())
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                            Simpan Jadwal
                        </button>
                    @else
                        <button
                            type="button"
                            disabled
                            class="inline-flex items-center rounded-xl bg-slate-300 px-5 py-3 text-sm font-medium text-slate-700 shadow-sm cursor-not-allowed">
                            Simpan Jadwal
                        </button>
                    @endif

                    <a
                        href="{{ route('superadmin.absensi.jadwal-kerja.index', ['category_id' => $categoryId]) }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection