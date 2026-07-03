@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Tipe Pegawai</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui data tipe pegawai.</p>
    </div>

    <div class="max-w-4xl">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-700">Form Edit Tipe Pegawai</h2>
            </div>

            <form action="{{ route('superadmin.tipe-pegawai.update', $employeeType->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $employeeType->name) }}"
                            placeholder="Contoh: ASN Tetap"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="code" class="mb-2 block text-sm font-medium text-slate-700">
                            Kode
                        </label>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            value="{{ old('code', $employeeType->code) }}"
                            placeholder="Contoh: ASN"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('code')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                            Deskripsi
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            placeholder="Masukkan deskripsi tipe pegawai..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >{{ old('description', $employeeType->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="mb-2 block text-sm font-medium text-slate-700">
                            Urutan
                        </label>
                        <input
                            type="number"
                            name="priority"
                            id="priority"
                            value="{{ old('priority', $employeeType->priority) }}"
                            placeholder="0"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('priority')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col justify-end gap-4">
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <input
                                type="checkbox"
                                name="is_honorarium"
                                value="1"
                                {{ old('is_honorarium', $employeeType->is_honorarium) ? 'checked' : '' }}
                                class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            <div>
                                <p class="text-sm font-medium text-slate-700">Honorarium</p>
                                <p class="text-xs text-slate-500">Centang jika tipe pegawai termasuk honorarium</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $employeeType->is_active) ? 'checked' : '' }}
                                class="h-5 w-5 rounded border-slate-300 text-green-600 focus:ring-green-500"
                            >
                            <div>
                                <p class="text-sm font-medium text-slate-700">Status Aktif</p>
                                <p class="text-xs text-slate-500">Data akan aktif dan bisa digunakan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-amber-500 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-amber-600"
                    >
                        Update
                    </button>

                    <a
                        href="{{ route('superadmin.tipe-pegawai.index') }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection