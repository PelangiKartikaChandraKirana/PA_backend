@extends('layouts.app')

@section('content')
<div class="p-6 max-w-xl">

    <h1 class="text-2xl font-bold text-gray-800 mb-4">
        Edit Kategori Jadwal Kerja
    </h1>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.absensi.kategori-jadwal-kerja.update', $item->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm font-medium">Nama Kategori</label>
            <input type="text" name="name"
                   value="{{ old('name', $item->name) }}"
                   class="w-full border rounded-lg p-2">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-sm font-medium">Start Date</label>
                <input type="date" name="start_date"
                       value="{{ old('start_date', \Carbon\Carbon::parse($item->start_date)->format('Y-m-d')) }}"
                       class="w-full border rounded-lg p-2">
            </div>

            <div>
                <label class="text-sm font-medium">End Date</label>
                <input type="date" name="end_date"
                       value="{{ old('end_date', \Carbon\Carbon::parse($item->end_date)->format('Y-m-d')) }}"
                       class="w-full border rounded-lg p-2">
            </div>
        </div>

        <div>
            <label class="text-sm font-medium">Prioritas</label>
            <input type="number" name="priority" min="1"
                   value="{{ old('priority', $item->priority) }}"
                   class="w-full border rounded-lg p-2">
        </div>

        <div class="flex gap-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Update
            </button>

            <a href="{{ route('superadmin.absensi.kategori-jadwal-kerja.index') }}"
               class="px-4 py-2 rounded-lg border">
                Batal
            </a>
        </div>

    </form>

</div>
@endsection