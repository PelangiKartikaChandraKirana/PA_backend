@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tambah Pegawai</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Daftarkan pegawai baru ke dalam sistem SIAPMAN.</p>
        </div>
        <a href="{{ route('superadmin.pegawai.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('superadmin.pegawai.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Informasi Dasar</h2>
                </div>

                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- NIP -->
                    <div class="space-y-2">
                        <label for="nip" class="text-sm font-semibold text-slate-700">NIP / Identitas <span class="text-rose-500">*</span></label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none"
                               placeholder="Masukkan NIP">
                        @error('nip')
                            <p class="text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none"
                               placeholder="Nama Lengkap Beserta Gelar">
                        @error('name')
                            <p class="text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="space-y-2">
                        <label for="position_id" class="text-sm font-semibold text-slate-700">Jabatan</label>
                        <select name="position_id" id="position_id" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unit Kerja -->
                    <div class="space-y-2">
                        <label for="department_id" class="text-sm font-semibold text-slate-700">Unit Kerja / Instansi</label>
                        <select name="department_id" id="department_id" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipe Pegawai -->
                    <div class="space-y-2">
                        <label for="employee_type_id" class="text-sm font-semibold text-slate-700">Tipe Pegawai</label>
                        <select name="employee_type_id" id="employee_type_id" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <option value="">-- Pilih Tipe --</option>
                            @foreach($employeeTypes as $type)
                                <option value="{{ $type->id }}" {{ old('employee_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- TPP Allowance -->
                    <div class="space-y-2">
                        <label for="tpp_allowance" class="text-sm font-semibold text-slate-700">Besaran TPP (Rp)</label>
                        <input type="number" name="tpp_allowance" id="tpp_allowance" value="{{ old('tpp_allowance', 0) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none"
                               placeholder="Contoh: 2500000">
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label for="status" class="text-sm font-semibold text-slate-700">Status</label>
                        <select name="status" id="status" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/30 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="reset" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
                        Reset
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Data Pegawai
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection