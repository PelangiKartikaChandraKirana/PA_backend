<table class="min-w-full divide-y divide-slate-200">
    <thead class="bg-slate-50">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">No</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">NIP / Nomor Induk</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Nama Lengkap</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Unit Kerja / OPD</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Hari Kerja</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Alpha</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Cuti/Izin</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL1</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL2</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL3</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">TL4</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW1</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW2</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW3</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">PSW4</th>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Rata-rata Durasi</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-slate-100 bg-white">
        @foreach($rows as $row)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-sm text-slate-700">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 text-sm text-slate-700">{{ $row->employee_id_number }}</td>
                <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $row->employee_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->company_name }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->hari_kerja }}</td>
                <td class="px-4 py-3 text-sm text-slate-600 text-center font-bold {{ $row->tidak_hadir > 0 ? 'text-red-500' : '' }}">{{ $row->tidak_hadir }}</td>
                <td class="px-4 py-3 text-sm text-slate-600 text-center font-bold {{ $row->cuti_izin > 0 ? 'text-purple-500' : '' }}">{{ $row->cuti_izin }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl1 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl2 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl3 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->tl4 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw1 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw2 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw3 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->psw4 }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $row->avg_durasi }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
