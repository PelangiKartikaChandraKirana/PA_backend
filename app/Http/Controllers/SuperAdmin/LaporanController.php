<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function presensiHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $tanggalObj = Carbon::parse($tanggal);

        $employeeQuery = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id');

        $selects = [
            'employees.id',
            'employees.name as employee_name',
            'employees.nip as employee_id_number',
            'positions.name as position_name',
            'departments.name as company_name',
        ];

        $rows = $employeeQuery
            ->select($selects)
            ->orderBy('employees.name')
            ->get();

        $absenceDocs = DB::table('absence_documents')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $tanggal)
            ->whereDate('end_date', '>=', $tanggal)
            ->get()
            ->keyBy('employee_id');

        $data = $rows->map(function ($row) use ($tanggal, $tanggalObj, $absenceDocs) {
            $attendanceLog = DB::table('attendance_logs')
    ->where('employee_id', $row->id)
    ->whereDate('attendance_date', $tanggal)
    ->first();
                $scanQuery = DB::table('attendance_scan_logs')
                ->join('attendance_logs', 'attendance_scan_logs.attendance_log_id', '=', 'attendance_logs.id')
                ->where('attendance_logs.employee_id', $row->id)
                ->whereDate('attendance_scan_logs.attendance_time', $tanggal)
                ->orderBy('attendance_scan_logs.attendance_time');

            if (Schema::hasTable('attendance_log_statuses') && Schema::hasColumn('attendance_logs', 'status_id')) {
                $scanQuery->leftJoin(
                    'attendance_log_statuses',
                    'attendance_logs.status_id',
                    '=',
                    'attendance_log_statuses.id'
                );
            }

            $scanSelect = [
                'attendance_scan_logs.*',
                'attendance_logs.status_id',
                'attendance_logs.validation_status',
                'attendance_logs.validation_reason',
                'attendance_logs.time_difference_seconds',
                'attendance_logs.check_in_photo_path',
                'attendance_logs.check_out_photo_path',
            ];

            if (Schema::hasTable('attendance_log_statuses') && Schema::hasColumn('attendance_logs', 'status_id')) {
                $scanSelect[] = 'attendance_log_statuses.name as status_name';
            } else {
                $scanSelect[] = DB::raw("NULL as status_name");
            }

            $logs = $scanQuery->select($scanSelect)->get();

            $firstLog = $logs->first();
            $lastLog = $logs->last();

	    $rejectedLog = null;
	    if (!$firstLog) {
            $rejectedLog = DB::table('attendance_logs')
        ->where('employee_id', $row->id)
        ->whereDate('attendance_date', $tanggal)
        ->where('validation_status', 'REJECTED')
        ->orderByDesc('created_at')
        ->first();
        }

            $jamMasuk = $attendanceLog->check_in_at ?? '-';
$jamKeluar = $attendanceLog->check_out_at ?? '-';

$durasiKerja = '-';
if ($attendanceLog && $attendanceLog->check_in_at && $attendanceLog->check_out_at) {
    $start = Carbon::parse($tanggal . ' ' . $attendanceLog->check_in_at);
    $end = Carbon::parse($tanggal . ' ' . $attendanceLog->check_out_at);

    if ($end->greaterThan($start)) {
        $minutes = $start->diffInMinutes($end);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        $durasiKerja = sprintf('%02d:%02d', $hours, $mins);
    }
}
              
            
            $absence = $absenceDocs->get($row->id);
            $status = 'Belum Ada Log';
            $keterlambatan = '0 menit';

            if ($firstLog) {
                $status = $firstLog->status_name ?? 'Hadir';
                if ($jamMasuk !== '-') {
                    $checkInTime = Carbon::parse($tanggal . ' ' . $jamMasuk);
                    $batasMasuk = $tanggalObj->isMonday() ? Carbon::parse($tanggal . ' 08:15:00') : Carbon::parse($tanggal . ' 07:30:00');
                    if ($checkInTime->greaterThan($batasMasuk)) {
                        $keterlambatan = $checkInTime->diffInMinutes($batasMasuk) . ' menit';
                        if (str_contains(strtolower($status), 'hadir')) {
                            $status = 'Terlambat';
                        }
                    }
                }
	    } elseif ($rejectedLog) {
    $reasonLabels = [
        'OUTSIDE_GEOFENCE' => 'Ditolak (Di Luar Radius)',
        'TIME_NOT_SYNC' => 'Ditolak (Waktu Tidak Sinkron)',
    ];
    $status = $reasonLabels[$rejectedLog->validation_reason] ?? 'Ditolak';
    $keterlambatan = '-';

            } elseif ($absence) {
                $status = strtoupper($absence->document_type ?? 'Cuti/Izin');
            } else {
                if ($tanggalObj->isWeekend()) {
                    $status = 'Libur';
                } elseif ($tanggalObj->isFuture()) {
                    $status = 'Belum Waktunya';
                } elseif ($tanggalObj->isToday()) {
                    $status = 'Belum Absen';
                } else {
                    $status = 'Alpha';
                }
            }

            return (object) [
                'employee_id_number' => $row->employee_id_number ?? '-',
                'employee_name' => $row->employee_name ?? '-',
                'position_name' => $row->position_name ?? '-',
                'company_name' => $row->company_name ?? '-',
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $jamKeluar,
                'durasi_kerja' => $durasiKerja,
                'status' => $status,
                'validation_status' => $rejectedLog->validation_status ?? 'VALID',
    'validation_reason' => $rejectedLog->validation_reason ?? null,
                'keterlambatan' => $keterlambatan,
                'lokasi_mesin' => ($firstLog && $firstLog->latitude && $firstLog->longitude)
    ? $firstLog->latitude . ', ' . $firstLog->longitude
    : '-',
'ip_address' => $firstLog->request_ip ?? '-',
                'foto_capture' => $this->resolvePhotoUrl($firstLog->face_image_path ?? $firstLog->check_in_photo_path ?? null),
            ];
        });

        if ($request->has('export') && $request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanHarianExport($data), 
                'Laporan_Presensi_Harian_' . $tanggal . '.xlsx'
            );
        }

        return view('superadmin.laporan.presensi-harian', compact('data', 'tanggal'));
    }

    public function presensiBulanan(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $employeeQuery = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id');

        $selects = [
            'employees.id',
            'employees.name as employee_name',
            'employees.nip as employee_id_number',
            'positions.name as position_name',
            'departments.name as company_name',
        ];

        $employees = $employeeQuery
            ->select($selects)
            ->orderBy('employees.name')
            ->get();

        $totalDaysInMonth = Carbon::createFromDate($tahun, $bulan)->daysInMonth;
        $workDaysInMonth = 0;
        for ($i = 1; $i <= $totalDaysInMonth; $i++) {
            $d = Carbon::createFromDate($tahun, $bulan, $i);
            if (!$d->isWeekend()) {
                if ($d->isPast() || $d->isToday()) {
                    $workDaysInMonth++;
                }
            }
        }

        $absenceDocs = DB::table('absence_documents')
            ->where('status', 'approved')
            ->whereYear('start_date', '<=', $tahun)
            ->whereYear('end_date', '>=', $tahun)
            ->get();

        $rows = $employees->map(function ($employee) use ($bulan, $tahun, $workDaysInMonth, $absenceDocs) {
            $logs = DB::table('attendance_logs')
                ->where('employee_id', $employee->id)
                ->whereYear('attendance_date', $tahun)
                ->whereMonth('attendance_date', $bulan)
                ->orderBy('attendance_date')
                ->get();

            $hariKerja = $logs->count();
            
            // Calculate Leave Days (Cuti/Izin)
            $cutiDays = 0;
            $employeeAbsences = $absenceDocs->where('employee_id', $employee->id);
            foreach ($employeeAbsences as $abs) {
                $start = Carbon::parse($abs->start_date);
                $end = Carbon::parse($abs->end_date);
                
                // Iterasi hari cuti, pastikan di bulan dan tahun yang sesuai, dan bukan weekend
                $curr = $start->copy();
                while ($curr->lte($end)) {
                    if ($curr->month == $bulan && $curr->year == $tahun && !$curr->isWeekend()) {
                        $cutiDays++;
                    }
                    $curr->addDay();
                }
            }

            $tidakHadir = max(0, $workDaysInMonth - $hariKerja - $cutiDays);

            $lateMinutes = 0;
            $earlyLeaveMinutes = 0;
            $durasiTotalMinutes = 0;

            foreach ($logs as $log) {
                if (!empty($log->check_in_at)) {
                    $date = Carbon::parse($log->attendance_date);
                    $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_in_at);
                    $lateMinutes += $this->calculateLateMinutes($date, $checkIn);
                }

                if (!empty($log->check_out_at)) {
                    $date = Carbon::parse($log->attendance_date);
                    $checkOut = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_out_at);
                    $earlyLeaveMinutes += $this->calculateEarlyLeaveMinutes($date, $checkOut);
                }

                if (!empty($log->check_in_at) && !empty($log->check_out_at)) {
                    $start = Carbon::parse($log->attendance_date . ' ' . $log->check_in_at);
                    $end = Carbon::parse($log->attendance_date . ' ' . $log->check_out_at);

                    if ($end->greaterThan($start)) {
                        $durasiTotalMinutes += $start->diffInMinutes($end);
                    }
                }
            }

            $tl1 = $this->countRange($logs, 1, 30, true);
            $tl2 = $this->countRange($logs, 31, 60, true);
            $tl3 = $this->countRange($logs, 61, 90, true);
            $tl4 = $this->countAbove($logs, 90, true);

            $psw1 = $this->countRange($logs, 1, 30, false);
            $psw2 = $this->countRange($logs, 31, 60, false);
            $psw3 = $this->countRange($logs, 61, 90, false);
            $psw4 = $this->countAbove($logs, 90, false);

            $avgDurasi = $hariKerja > 0
                ? sprintf('%02d:%02d', floor(($durasiTotalMinutes / $hariKerja) / 60), ($durasiTotalMinutes / $hariKerja) % 60)
                : '-';

            return (object) [
                'employee_id_number' => $employee->employee_id_number ?? '-',
                'employee_name' => $employee->employee_name ?? '-',
                'position_name' => $employee->position_name ?? '-',
                'company_name' => $employee->company_name ?? '-',
                'hari_kerja' => $hariKerja,
                'tidak_hadir' => $tidakHadir,
                'cuti_izin' => $cutiDays,
                'tl1' => $tl1,
                'tl2' => $tl2,
                'tl3' => $tl3,
                'tl4' => $tl4,
                'psw1' => $psw1,
                'psw2' => $psw2,
                'psw3' => $psw3,
                'psw4' => $psw4,
                'avg_durasi' => $avgDurasi,
                'logs' => $logs,
            ];
        });

        $totalPegawai = $employees->count();
        $totalHadir = $rows->sum('hari_kerja');
        $totalTidakHadir = $rows->sum('tidak_hadir');
        $totalCuti = $rows->sum('cuti_izin');
        $totalTerlambat = $rows->sum(fn ($row) => $row->tl1 + $row->tl2 + $row->tl3 + $row->tl4);
        $totalPulangCepat = $rows->sum(fn ($row) => $row->psw1 + $row->psw2 + $row->psw3 + $row->psw4);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        if ($request->has('export') && $request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanBulananExport($rows), 
                'Laporan_Presensi_Bulanan_' . $months[(int)$bulan] . '_' . $tahun . '.xlsx'
            );
        }

        return view('superadmin.laporan.presensi-bulanan', compact(
            'rows',
            'bulan',
            'tahun',
            'totalPegawai',
            'totalHadir',
            'totalTidakHadir',
            'totalCuti',
            'totalTerlambat',
            'totalPulangCepat'
        ));
    }

    private function calculateLateMinutes(Carbon $date, Carbon $checkIn): int
    {
        $targetMinutes = $date->isMonday() ? (8 * 60 + 15) : (7 * 60 + 30);
        $actualMinutes = $checkIn->hour * 60 + $checkIn->minute;

        return max(0, $actualMinutes - $targetMinutes);
    }

    private function calculateEarlyLeaveMinutes(Carbon $date, Carbon $checkOut): int
    {
        $targetMinutes = $date->isFriday() ? (15 * 60) : (15 * 60 + 30);
        $actualMinutes = $checkOut->hour * 60 + $checkOut->minute;

        return max(0, $targetMinutes - $actualMinutes);
    }

    private function countRange($logs, int $min, int $max, bool $late = true): int
    {
        return collect($logs)->filter(function ($log) use ($min, $max, $late) {
            if ($late) {
                if (empty($log->check_in_at) || empty($log->attendance_date)) {
                    return false;
                }

                $date = Carbon::parse($log->attendance_date);
                $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_in_at);
                $minutes = $this->calculateLateMinutes($date, $checkIn);

                return $minutes >= $min && $minutes <= $max;
            }

            if (empty($log->check_out_at) || empty($log->attendance_date)) {
                return false;
            }

            $date = Carbon::parse($log->attendance_date);
            $checkOut = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_out_at);
            $minutes = $this->calculateEarlyLeaveMinutes($date, $checkOut);

            return $minutes >= $min && $minutes <= $max;
        })->count();
    }

    private function countAbove($logs, int $min, bool $late = true): int
    {
        return collect($logs)->filter(function ($log) use ($min, $late) {
            if ($late) {
                if (empty($log->check_in_at) || empty($log->attendance_date)) {
                    return false;
                }

                $date = Carbon::parse($log->attendance_date);
                $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_in_at);
                $minutes = $this->calculateLateMinutes($date, $checkIn);

                return $minutes > $min;
            }

            if (empty($log->check_out_at) || empty($log->attendance_date)) {
                return false;
            }

            $date = Carbon::parse($log->attendance_date);
            $checkOut = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_out_at);
            $minutes = $this->calculateEarlyLeaveMinutes($date, $checkOut);

            return $minutes > $min;
        })->count();
    }

    private function resolvePhotoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(Storage::url($path));
    }
}
