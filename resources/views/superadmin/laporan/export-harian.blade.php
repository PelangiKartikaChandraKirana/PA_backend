<table class="min-w-full divide-y divide-slate-200">
    <thead class="bg-slate-50">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">No</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">NIP / Nomor Induk</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Nama Lengkap</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jabatan</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Unit Kerja / OPD</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jam Masuk</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Jam Keluar</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Durasi Kerja</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Keterlambatan</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Lokasi / Mesin</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">IP / Device</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Foto Capture</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-slate-100 bg-white">
        @foreach($data as $row)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-sm text-slate-700">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ $row->employee_id_number ?? '-' }}</td>
                <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $row->employee_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->position_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->company_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->jam_masuk }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->jam_keluar }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->durasi_kerja }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->status }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->keterlambatan }} menit</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->lokasi_mesin }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->ip_address }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->foto_capture ? $row->foto_capture : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
