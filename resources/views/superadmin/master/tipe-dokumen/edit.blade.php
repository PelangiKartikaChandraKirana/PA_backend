@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Tipe Dokumen</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Perbarui pengaturan untuk tipe dokumen <b>{{ $item->name }}</b>.</p>
        </div>
        <a href="{{ route('superadmin.master.tipe-dokumen.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('superadmin.master.tipe-dokumen.update', $item->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Konfigurasi Dokumen</h2>
                </div>

                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nama Dokumen -->
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold text-slate-700">Nama Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none"
                               placeholder="Contoh: Surat Sakit Dokter">
                        @error('name')
                            <p class="text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kode -->
                    <div class="space-y-2">
                        <label for="code" class="text-sm font-semibold text-slate-700">Kode Unik <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $item->code) }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none uppercase"
                               placeholder="Contoh: SKD">
                        @error('code')
                            <p class="text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-2">
                        <label for="category" class="text-sm font-semibold text-slate-700">Kategori</label>
                        <select name="category" id="category" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <option value="Cuti" {{ old('category', $item->category) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Sakit" {{ old('category', $item->category) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Izin" {{ old('category', $item->category) == 'Izin' ? 'selected' : '' }}>Izin</option>
                            <option value="Tugas Luar" {{ old('category', $item->category) == 'Tugas Luar' ? 'selected' : '' }}>Tugas Luar</option>
                            <option value="Lainnya" {{ old('category', $item->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Warna -->
                    <div class="space-y-2">
                        <label for="color" class="text-sm font-semibold text-slate-700">Warna Label Visual</label>
                        <div class="flex items-center gap-4">
                            <input type="color" name="color" id="color" value="{{ old('color', $item->color ?? '#3b82f6') }}"
                                   class="h-10 w-20 cursor-pointer rounded-lg border border-slate-200 bg-white p-1 shadow-sm">
                            <span class="text-xs font-medium text-slate-500 italic">Identitas visual untuk laporan.</span>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi / Keterangan</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none"
                                  placeholder="Berikan penjelasan singkat...">{{ old('description', $item->description) }}</textarea>
                    </div>

                    <!-- Options -->
                    <div class="md:col-span-2 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/50 p-4 transition hover:bg-white hover:shadow-md">
                            <input type="hidden" name="requires_approval" value="0">
                            <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $item->requires_approval) ? 'checked' : '' }}
                                   class="h-5 w-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500/20">
                            <div>
                                <p class="text-xs font-bold text-slate-700">Approval</p>
                                <p class="text-[10px] text-slate-500 leading-tight">Butuh validasi admin</p>
                            </div>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/50 p-4 transition hover:bg-white hover:shadow-md">
                            <input type="hidden" name="is_required" value="0">
                            <input type="checkbox" name="is_required" value="1" {{ old('is_required', $item->is_required) ? 'checked' : '' }}
                                   class="h-5 w-5 rounded-lg border-slate-300 text-amber-500 focus:ring-amber-500/20">
                            <div>
                                <p class="text-xs font-bold text-slate-700">Lampiran</p>
                                <p class="text-[10px] text-slate-500 leading-tight">Wajib upload dokumen</p>
                            </div>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/50 p-4 transition hover:bg-white hover:shadow-md">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                                   class="h-5 w-5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500/20">
                            <div>
                                <p class="text-xs font-bold text-slate-700">Aktif</p>
                                <p class="text-[10px] text-slate-500 leading-tight">Status ketersediaan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/30 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Perbarui Tipe Dokumen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
