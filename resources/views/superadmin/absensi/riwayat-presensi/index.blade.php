cat > resources/views/superadmin/absensi/riwayat-presensi/index.blade.php <<'BLADE'
@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Riwayat Presensi</h1>

    <a href="{{ route('superadmin.absensi.riwayat-presensi.export', request()->query()) }}"
       class="bg-gray-800 text-white px-4 py-2 rounded">
      Export
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
      {{ session('success') }}
    </div>
  @endif

  <form method="GET" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-4 gap-3 items-end">
    <div class="col-span-2">
      <label class="text-sm text-gray-600">Cari Pegawai (Nama / NIP)</label>
      <input type="text" name="q" value="{{ $q }}"
             class="w-full border rounded px-3 py-2"
             placeholder="Nama / NIP...">
    </div>

    <div>
      <label class="text-sm text-gray-600">Tanggal</label>
      <input type="date" name="date" value="{{ $date }}"
             class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="text-sm text-gray-600">Status</label>
      <select name="status_id" class="w-full border rounded px-3 py-2">
        <option value="">-- Semua --</option>
        @foreach($statuses as $s)
          <option value="{{ $s->id }}" @selected((string)$statusId === (string)$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-span-4">
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
      <a href="{{ route('superadmin.absensi.riwayat-presensi.index') }}" class="ml-2 text-sm underline">Reset</a>
    </div>
  </form>

  <div class="bg-white shadow rounded">
    <table class="w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3">No</th>
          <th class="p-3">Pegawai</th>
          <th class="p-3">Tanggal</th>
          <th class="p-3">Masuk</th>
          <th class="p-3">Keluar</th>
          <th class="p-3">Status</th>
          <th class="p-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr class="border-b">
            <td class="p-3">{{ ($logs->currentPage()-1)*$logs->perPage() + $loop->iteration }}</td>
            <td class="p-3">{{ $log->employee->name ?? '-' }}</td>
            <td class="p-3">{{ \Illuminate\Support\Carbon::parse($log->attendance_date)->format('d-m-Y') }}</td>
            <td class="p-3">{{ $log->check_in_at ?? '-' }}</td>
            <td class="p-3">{{ $log->check_out_at ?? '-' }}</td>
            <td class="p-3">{{ $log->status->name ?? '-' }}</td>
            <td class="p-3">
              <a class="text-blue-600 underline"
                 href="{{ route('superadmin.absensi.riwayat-presensi.show', $log->id) }}">
                Detail
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="p-3 text-center text-gray-500">Belum ada data</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $logs->links() }}
  </div>
</div>
@endsection
BLADE