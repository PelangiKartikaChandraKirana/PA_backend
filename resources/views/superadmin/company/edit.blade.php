@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Instansi / OPD</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Perbarui data profil instansi <b>{{ $company->name }}</b>.</p>
        </div>
        <a href="{{ route('superadmin.master.instansi.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('superadmin.master.instansi.update', $company->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Profil Instansi</h2>
                </div>

                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nama -->
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold text-slate-700">Nama Instansi <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                    </div>

                    <!-- Kode OPD -->
                    <div class="space-y-2">
                        <label for="kode_opd" class="text-sm font-semibold text-slate-700">Kode OPD</label>
                        <input type="text" name="kode_opd" id="kode_opd" value="{{ old('kode_opd', $company->kode_opd) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none italic uppercase">
                    </div>

                    <!-- Singkatan -->
                    <div class="space-y-2">
                        <label for="short_name" class="text-sm font-semibold text-slate-700">Singkatan / Nama Pendek</label>
                        <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $company->short_name) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none uppercase tracking-widest">
                    </div>

                    <!-- Tipe -->
                    <div class="space-y-2">
                        <label for="type" class="text-sm font-semibold text-slate-700">Tipe Instansi</label>
                        <select name="type" id="type" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <option value="Dinas" {{ old('type', $company->type) == 'Dinas' ? 'selected' : '' }}>Dinas</option>
                            <option value="Badan" {{ old('type', $company->type) == 'Badan' ? 'selected' : '' }}>Badan</option>
                            <option value="Kecamatan" {{ old('type', $company->type) == 'Kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                            <option value="Puskesmas" {{ old('type', $company->type) == 'Puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                            <option value="UPTD" {{ old('type', $company->type) == 'UPTD' ? 'selected' : '' }}>UPTD</option>
                            <option value="Lainnya" {{ old('type', $company->type) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-semibold text-slate-700">Email Instansi</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                    </div>

                    <!-- Phone -->
                    <div class="space-y-2">
                        <label for="phone" class="text-sm font-semibold text-slate-700">No. Telepon / Fax</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2 space-y-2">
                        <label for="address" class="text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3"
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">{{ old('address', $company->address) }}</textarea>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/30 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Perbarui Data Instansi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection