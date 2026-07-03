<?php

namespace App\Exports;

use App\Models\AttendanceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceRecapExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $unitKerja;
    protected $month;
    protected $year;

    public function __construct($unitKerja, $month, $year)
    {
        $this->unitKerja = $unitKerja;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return AttendanceLog::with(['employee', 'status'])
            ->whereYear('attendance_date', $this->year)
            ->whereMonth('attendance_date', $this->month)
            ->whereHas('employee', function ($q) {
                if ($this->unitKerja) {
                    $q->where('unit_kerja', $this->unitKerja);
                }
            })
            ->orderBy('attendance_date', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Log',
            'Nama Pegawai',
            'NIP',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Catatan',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->employee->name ?? '-',
            $log->employee->nip ?? '-',
            $log->attendance_date->format('Y-m-d'),
            $log->check_in_at ?? '-',
            $log->check_out_at ?? '-',
            $log->status->name ?? '-',
            $log->note ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
