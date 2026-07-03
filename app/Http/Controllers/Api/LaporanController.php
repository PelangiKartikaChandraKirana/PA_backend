<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function presensiHarian(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());

        $user = $request->user();

        if (!$user || !$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
                'tanggal' => $tanggal,
                'data' => [],
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                'tanggal' => $tanggal,
                'data' => [],
            ], 404);
        }

        $employeeQuery = DB::table('employees');

        if (
            Schema::hasTable('companies') &&
            Schema::hasColumn('employees', 'company_id')
        ) {
            $employeeQuery->leftJoin('companies', 'employees.company_id', '=', 'companies.id');
        }

        $selects = [
            'employees.id',
            'employees.name as employee_name',
        ];

        if (Schema::hasColumn('employees', 'employee_id_number')) {
            $selects[] = 'employees.employee_id_number';
        } elseif (Schema::hasColumn('employees', 'nip')) {
            $selects[] = 'employees.nip as employee_id_number';
        } else {
            $selects[] = DB::raw("'-' as employee_id_number");
        }

        $selects[] = DB::raw("'-' as position_name");

        if (Schema::hasTable('companies') && Schema::hasColumn('employees', 'company_id')) {
            $selects[] = 'companies.name as company_name';
        } else {
            $selects[] = DB::raw("'-' as company_name");
        }

        $rows = $employeeQuery
            ->select($selects)
            ->where('employees.id', $employee->id)
            ->orderBy('employees.name')
            ->get();

        $data = $rows->map(function ($row) use ($tanggal) {
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

            $jamMasuk = '-';
            $jamKeluar = '-';

            if ($firstLog && $firstLog->attendance_time) {
                $jamMasuk = Carbon::parse($firstLog->attendance_time)->format('H:i:s');
            }

            if ($lastLog && $lastLog->attendance_time) {
                $jamKeluar = Carbon::parse($lastLog->attendance_time)->format('H:i:s');
            }

            $durasiKerja = '-';
            if ($firstLog && $lastLog && $firstLog->attendance_time && $lastLog->attendance_time) {
                $startTime = Carbon::parse($firstLog->attendance_time);
                $endTime = Carbon::parse($lastLog->attendance_time);

                if ($endTime->greaterThan($startTime)) {
                    $minutes = $startTime->diffInMinutes($endTime);
                    $hours = floor($minutes / 60);
                    $mins = $minutes % 60;
                    $durasiKerja = sprintf('%02d:%02d', $hours, $mins);
                }
            }

            return [
                'employee_id_number' => $row->employee_id_number ?? '-',
                'employee_name' => $row->employee_name ?? '-',
                'position_name' => $row->position_name ?? '-',
                'company_name' => $row->company_name ?? '-',
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $jamKeluar,
                'durasi_kerja' => $durasiKerja,
                'status' => $firstLog->status_name ?? ($firstLog ? 'Hadir' : 'Belum Ada Log'),
                'keterlambatan' => 0,
                'lokasi_mesin' => '-',
                'ip_address' => '-',
                'foto_capture' => $this->resolvePhotoUrl($firstLog->face_image_path ?? $firstLog->check_in_photo_path ?? null),
            ];
        });

        return response()->json([
            'message' => 'Laporan presensi harian berhasil diambil',
            'tanggal' => $tanggal,
            'data' => $data,
        ]);
    }

    public function presensiBulanan(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $user = $request->user();

        if (!$user || !$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
                'month' => $month,
                'year' => $year,
                'data' => [],
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                'month' => $month,
                'year' => $year,
                'data' => [],
            ], 404);
        }

        $query = DB::table('attendance_logs')
            ->join('employees', 'attendance_logs.employee_id', '=', 'employees.id');

        if (
            Schema::hasTable('companies') &&
            Schema::hasColumn('employees', 'company_id')
        ) {
            $query->leftJoin('companies', 'employees.company_id', '=', 'companies.id');
        }

        if (
            Schema::hasTable('attendance_log_statuses') &&
            Schema::hasColumn('attendance_logs', 'status_id')
        ) {
            $query->leftJoin(
                'attendance_log_statuses',
                'attendance_logs.status_id',
                '=',
                'attendance_log_statuses.id'
            );
        }

        $selects = [
            'attendance_logs.id',
            'attendance_logs.employee_id',
            'attendance_logs.attendance_date',
            'attendance_logs.check_in_at',
            'attendance_logs.check_out_at',
            'attendance_logs.created_at',
            'employees.name as employee_name',
        ];

        if (Schema::hasColumn('employees', 'employee_id_number')) {
            $selects[] = 'employees.employee_id_number';
        } elseif (Schema::hasColumn('employees', 'nip')) {
            $selects[] = 'employees.nip as employee_id_number';
        } else {
            $selects[] = DB::raw("'-' as employee_id_number");
        }

        if (Schema::hasTable('companies') && Schema::hasColumn('employees', 'company_id')) {
            $selects[] = 'companies.name as company_name';
        } else {
            $selects[] = DB::raw("'-' as company_name");
        }

        if (Schema::hasTable('attendance_log_statuses') && Schema::hasColumn('attendance_logs', 'status_id')) {
            $selects[] = 'attendance_log_statuses.name as status_name';
        } else {
            $selects[] = DB::raw("NULL as status_name");
        }

        $data = $query
            ->select($selects)
            ->where('attendance_logs.employee_id', $employee->id)
            ->whereYear('attendance_logs.attendance_date', $year)
            ->whereMonth('attendance_logs.attendance_date', $month)
            ->orderBy('attendance_logs.attendance_date', 'desc')
            ->orderBy('employees.name')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'employee_id' => $log->employee_id,
                    'employee_id_number' => $log->employee_id_number ?? '-',
                    'employee_name' => $log->employee_name ?? '-',
                    'company_name' => $log->company_name ?? '-',
                    'attendance_date' => optional($log->attendance_date)->format('Y-m-d') ?? $log->attendance_date,
                    'check_in_at' => $log->check_in_at,
                    'check_out_at' => $log->check_out_at,
                    'status' => $log->status_name ?? ($log->check_in_at ? 'Hadir' : 'Belum Ada Log'),
                    'keterlambatan' => 0,
                ];
            });

        return response()->json([
            'message' => 'Laporan presensi bulanan berhasil diambil',
            'month' => $month,
            'year' => $year,
            'data' => $data,
        ]);
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
