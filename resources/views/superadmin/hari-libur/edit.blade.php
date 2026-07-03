@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Hari Libur</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui data hari libur.</p>
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
                <h2 class="text-lg font-semibold text-slate-700">
                    Form Edit Hari Libur
                </h2>
            </div>

            <form action="{{ route('superadmin.hari-libur.update',$holiday->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    {{-- NAMA --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Nama Hari Libur
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name',$holiday->name) }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>


                    {{-- TANGGAL --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="date"
                            value="{{ old('date',$holiday->date) }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>


                    {{-- JENIS --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Jenis Libur
                        </label>

                        <select
                            name="type"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="Nasional" {{ $holiday->type == 'Nasional' ? 'selected' : '' }}>
                                Nasional
                            </option>

                            <option value="Cuti Bersama" {{ $holiday->type == 'Cuti Bersama' ? 'selected' : '' }}>
                                Cuti Bersama
                            </option>

                            <option value="Hari Besar Agama" {{ $holiday->type == 'Hari Besar Agama' ? 'selected' : '' }}>
                                Hari Besar Agama
                            </option>

                            <option value="Lainnya" {{ $holiday->type == 'Lainnya' ? 'selected' : '' }}>
                                Lainnya
                            </option>

                        </select>
                    </div>


                    {{-- TAHUN --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Tahun
                        </label>

                        <input
                            type="number"
                            name="year"
                            value="{{ old('year',$holiday->year) }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>


                    {{-- INSTANSI --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Instansi ID
                        </label>

                        <input
                            type="number"
                            name="company_id"
                            value="{{ old('company_id',$holiday->company_id) }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>


                    {{-- CHECKBOX --}}
                    <div class="flex flex-col justify-end gap-4">

                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                            <input
                                type="checkbox"
                                name="is_nasional"
                                value="1"
                                {{ $holiday->is_nasional ? 'checked' : '' }}
                                class="h-5 w-5 text-green-600"
                            >

                            <div>
                                <p class="text-sm font-medium text-slate-700">
                                    Libur Nasional
                                </p>
                            </div>

                        </label>


                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                            <input
                                type="checkbox"
                                name="is_recurring"
                                value="1"
                                {{ $holiday->is_recurring ? 'checked' : '' }}
                                class="h-5 w-5 text-blue-600"
                            >

                            <div>
                                <p class="text-sm font-medium text-slate-700">
                                    Berulang Tiap Tahun
                                </p>
                            </div>

                        </label>

                    </div>


                    {{-- DESKRIPSI --}}
                    <div class="md:col-span-2">

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >{{ old('description',$holiday->description) }}</textarea>

                    </div>

                </div>


                {{-- BUTTON --}}
                <div class="mt-8 flex gap-3 border-t border-slate-200 pt-6">

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-3 text-sm text-white hover:bg-blue-700"
                    >
                        Update
                    </button>

                    <a
                        href="{{ route('superadmin.hari-libur.index') }}"
                        class="rounded-xl border border-slate-300 px-5 py-3 text-sm text-slate-700 hover:bg-slate-50"
                    >
                        Kembali
                    </a>

                </div>

            </form>

        </div>
    </div>
</div>
@endsection