@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Jadwal Kerja</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui informasi jadwal, jam masuk, jam pulang, dan toleransi keterlambatan.</p>
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
                        <h2 class="text-lg font-semibold text-slate-700">Informasi Jadwal</h2>
                        <p class="mt-1 text-sm text-slate-500">Sesuaikan data di bawah ini.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.absensi.jadwal-kerja.update', $item->id) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-1">
                        <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">
                            Kategori Jadwal <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            id="category_id"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('category_id') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                        >
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('category_id', $item->category_id) === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label for="day_of_week" class="mb-2 block text-sm font-medium text-slate-700">
                            Hari Kerja <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="day_of_week"
                            id="day_of_week"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('day_of_week') border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                            required>
                            @for($i=1;$i<=7;$i++)
                                @php $hari=[1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu'][$i]; @endphp
                                <option value="{{ $i }}" {{ (string)old('day_of_week', $item->day_of_week) === (string)$i ? 'selected' : '' }}>
                                    {{ $hari }}
                                </option>
                            @endfor
                        </select>
                        @error('day_of_week')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">Jam Absensi ASN</p>
                                    <p class="text-sm text-slate-500">Tentukan jam masuk dan pulang untuk jadwal kerja ini.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mt-4">
                                <div class="md:col-span-1">
                                    <label for="start_time" class="mb-2 block text-sm font-medium text-slate-700">
                                        Jam Masuk <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="time"
                                        name="start_time"
                                        id="start_time"
                                        value="{{ old('start_time', \Carbon\Carbon::parse($item->start_time)->format('H:i')) }}"
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
                                        value="{{ old('end_time', \Carbon\Carbon::parse($item->end_time)->format('H:i')) }}"
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
                                        Toleransi Keterlambatan (menit)
                                    </label>
                                    <input
                                        type="number"
                                        name="tolerance_minutes"
                                        id="tolerance_minutes"
                                        value="{{ old('tolerance_minutes', $item->tolerance_minutes) }}"
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
                                        value="{{ old('effective_minutes', $item->effective_minutes) }}"
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
                                        value="{{ old('employee_type', $item->employee_type) }}"
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
                                            {{ old('is_active', $item->is_active) ? 'checked' : '' }}
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
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
                        Update Jadwal
                    </button>

                    <a
                        href="{{ route('superadmin.absensi.jadwal-kerja.index', ['category_id' => $item->category_id]) }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection