<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\AbsenceDocument;
use App\Models\FaultReport;
use App\Exports\AttendanceRecapExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $data = $this->getDashboardStats();
        return view('admin.dashboard', $data);
    }

    public function stats()
    {
        return response()->json($this->getDashboardStats());
    }

    // 🔥 TAMBAHAN: HALAMAN PRESENSI ADMIN
    public function presensi()
    {
        $admin = auth('web')->user();
        $unitKerja = $admin?->unit_kerja;
        $today = Carbon::today()->toDateString();
        $todayObj = Carbon::parse($today);

        $users = User::with([
            'employee.attendanceLogs' => function($q) use ($today) {
                $q->whereDate('attendance_date', $today);
            },
            'employee.absenceDocuments' => function($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            }
        ])
        ->where('role', 'user')
        ->when($unitKerja, function ($query) use ($unitKerja) {
            $query->where('unit_kerja', $unitKerja);
        })
        ->get();

        $data = $users->map(function ($user) use ($today, $todayObj) {
            $log = $user->employee?->attendanceLogs->first();
            $absence = $user->employee?->absenceDocuments->first();
            
            $status = 'Belum Ada Log';
            
            if ($log) {
                $status = $log->status->name ?? 'Hadir';
                if ($log->check_in_at) {
                    $checkInTime = Carbon::parse($today . ' ' . $log->check_in_at);
                    $batasMasuk = $todayObj->isMonday() ? Carbon::parse($today . ' 08:15:00') : Carbon::parse($today . ' 07:30:00');
                    if ($checkInTime->greaterThan($batasMasuk)) {
                        if (str_contains(strtolower($status), 'hadir')) {
                            $status = 'Terlambat';
                        }
                    }
                }
            } elseif ($absence) {
                $status = strtoupper($absence->document_type ?? 'Cuti/Izin');
            } else {
                if ($todayObj->isWeekend()) {
                    $status = 'Libur';
                } elseif ($todayObj->isFuture()) {
                    $status = 'Belum Waktunya';
                } elseif ($todayObj->isToday()) {
                    $status = 'Belum Absen';
                } else {
                    $status = 'Alpha';
                }
            }

            return (object) [
                'user' => $user,
                'employee' => $user->employee,
                'log' => $log,
                'status' => $status,
                'absence' => $absence,
            ];
        });

        return view('admin.presensi', compact('data', 'today'));
    }

    public function pegawai()
    {
        $admin = auth('web')->user();
        $unitKerja = $admin?->unit_kerja;

        $pegawai = User::with('employee.position', 'employee.department')
            ->where('role', 'user')
            ->when($unitKerja, function ($query) use ($unitKerja) {
                $query->where('unit_kerja', $unitKerja);
            })
            ->get();

        // 🔥 CALCULATE COMPLIANCE SCORE
        $pegawai->map(function ($user) {
            $last30Days = Carbon::today()->subDays(30);
            $presentCount = AttendanceLog::where('employee_id', $user->id)
                ->where('attendance_date', '>=', $last30Days)
                ->count();
            
            // Assume 22 workdays in a month
            $user->compliance_score = min(round(($presentCount / 22) * 100), 100);
            return $user;
        });

        return view('admin.pegawai', compact('pegawai', 'unitKerja'));
    }

    public function showPegawai($id)
    {
        $admin = auth('web')->user();
        $unitKerja = $admin?->unit_kerja;

        $user = User::with([
            'employee.position', 
            'employee.department', 
            'employee.absenceDocuments' => function($q) {
                $q->orderBy('created_at', 'desc')->take(5);
            },
            'employee.attendanceLogs' => function($q) {
                $q->orderBy('attendance_date', 'desc')->take(10);
            }
        ])
        ->where('role', 'user')
        ->when($unitKerja, function ($query) use ($unitKerja) {
            $query->where('unit_kerja', $unitKerja);
        })
        ->findOrFail($id);

        // 🔥 CALCULATE COMPLIANCE SCORE
        $last30Days = Carbon::today()->subDays(30);
        $presentCount = AttendanceLog::where('employee_id', $user->id)
            ->where('attendance_date', '>=', $last30Days)
            ->count();
        $user->compliance_score = min(round(($presentCount / 22) * 100), 100);

        return view('admin.pegawai.show', compact('user', 'unitKerja'));
    }

    // 🔥 TAMBAHAN: HALAMAN MONITORING PRESENSI
    public function monitoring(Request $request)
    {
        $admin = auth('web')->user();
        $unitKerja = $admin?->unit_kerja;

        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $year = substr($selectedMonth, 0, 4);
        $month = substr($selectedMonth, 5, 2);

        $users = User::with([
            'employee.attendanceLogs' => function($q) use ($year, $month) {
                $q->whereYear('attendance_date', $year)->whereMonth('attendance_date', $month);
            },
            'employee.absenceDocuments' => function($q) use ($year) {
                $q->where('status', 'approved')
                  ->whereYear('start_date', '<=', $year)
                  ->whereYear('end_date', '>=', $year);
            }
        ])
        ->where('role', 'user')
        ->when($unitKerja, function ($query) use ($unitKerja) {
            $query->where('unit_kerja', $unitKerja);
        })
        ->paginate(15)
        ->withQueryString();

        $totalDaysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        $workDaysInMonth = 0;
        for ($i = 1; $i <= $totalDaysInMonth; $i++) {
            $d = Carbon::createFromDate($year, $month, $i);
            if (!$d->isWeekend()) {
                if ($d->isPast() || $d->isToday()) {
                    $workDaysInMonth++;
                }
            }
        }

        $globalOntime = 0;
        $globalLate = 0;
        $globalAbsent = 0;
        $globalTotalLogs = 0;

        $users->getCollection()->transform(function($user) use ($year, $month, $workDaysInMonth, &$globalOntime, &$globalLate, &$globalAbsent, &$globalTotalLogs) {
            $logs = $user->employee?->attendanceLogs ?? collect();
            $absences = $user->employee?->absenceDocuments ?? collect();

            $hariKerja = $logs->count();
            $globalTotalLogs += $hariKerja;

            $cutiDays = 0;
            foreach ($absences as $abs) {
                $start = Carbon::parse($abs->start_date);
                $end = Carbon::parse($abs->end_date);
                $curr = $start->copy();
                while ($curr->lte($end)) {
                    if ($curr->month == $month && $curr->year == $year && !$curr->isWeekend()) {
                        $cutiDays++;
                    }
                    $curr->addDay();
                }
            }

            $tidakHadir = max(0, $workDaysInMonth - $hariKerja - $cutiDays);
            $globalAbsent += $cutiDays + $tidakHadir;

            $lateDays = 0;
            foreach ($logs as $log) {
                if (!empty($log->check_in_at)) {
                    $date = Carbon::parse($log->attendance_date);
                    $checkIn = Carbon::parse($date->format('Y-m-d') . ' ' . $log->check_in_at);
                    
                    // Gunakan fungsi logic yang sama dengan SuperAdmin
                    $targetMinutes = $date->isMonday() ? (8 * 60 + 15) : (7 * 60 + 30);
                    $actualMinutes = $checkIn->hour * 60 + $checkIn->minute;
                    
                    if ($actualMinutes > $targetMinutes) {
                        $lateDays++;
                        $globalLate++;
                    } else {
                        $globalOntime++;
                    }
                }
            }

            $user->stats = (object) [
                'hari_kerja' => $hariKerja,
                'cuti_izin' => $cutiDays,
                'alpha' => $tidakHadir,
                'terlambat' => $lateDays,
            ];

            return $user;
        });

        // 🔥 CALCULATE SUMMARY STATS FOR THE MONTH
        // We're calculating it just for the paginated page for now, or we can use global queries.
        // For accuracy, let's just query globally for the stats panel:
        $globalUsersQuery = User::where('role', 'user')
            ->when($unitKerja, function ($query) use ($unitKerja) {
                $query->where('unit_kerja', $unitKerja);
            })->pluck('id');
            
        $globalEmployeeIds = \App\Models\Employee::whereIn('nip', User::whereIn('id', $globalUsersQuery)->pluck('nip'))->pluck('id');
        
        $totalLogsAll = AttendanceLog::whereIn('employee_id', $globalEmployeeIds)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)->count();
            
        $lateLogsAll = AttendanceLog::whereIn('employee_id', $globalEmployeeIds)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->whereHas('status', function($q) {
                $q->where('name', 'like', '%terlambat%');
            })->count();

        $stats = [
            'total_logs' => $totalLogsAll,
            'ontime' => max(0, $totalLogsAll - $lateLogsAll), // Simplified
            'late' => $lateLogsAll,
            'absent' => AbsenceDocument::whereIn('employee_id', $globalEmployeeIds)
                ->whereYear('start_date', $year)
                ->whereMonth('start_date', $month)
                ->where('status', 'approved')
                ->count(),
        ];

        $data = $users;

        return view('admin.monitoring', compact('data', 'selectedMonth', 'unitKerja', 'stats'));
    }

    public function exportMonitoring(Request $request)
    {
        $admin = auth('web')->user();
        $unitKerja = $admin?->unit_kerja;
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $year = substr($selectedMonth, 0, 4);
        $month = substr($selectedMonth, 5, 2);

        $fileName = 'Rekap_Kehadiran_' . ($unitKerja ?? 'Unit') . '_' . $selectedMonth . '.xlsx';

        return Excel::download(new AttendanceRecapExport($unitKerja, $month, $year), $fileName);
    }

    private function getDashboardStats(): array
    {
        $admin = auth('web')->user();
        $unitKerja = $admin?->unit_kerja;
        $today = Carbon::today()->toDateString();

        // 🔹 TOTAL PEGAWAI
        $totalPegawai = User::where('role', 'user')
            ->when($unitKerja, function ($query) use ($unitKerja) {
                $query->where('unit_kerja', $unitKerja);
            })
            ->count();

        // 🔹 USER AKTIF
        $userAktif = User::where('role', 'user')
            ->when($unitKerja, function ($query) use ($unitKerja) {
                $query->where('unit_kerja', $unitKerja);
            })
            ->where('status', 'aktif')
            ->count();

        // 🔹 HADIR HARI INI
        $hadirHariIni = AttendanceLog::whereDate('attendance_date', $today)
            ->distinct('employee_id')
            ->count('employee_id');

        // 🔹 BELUM PRESENSI
        $belumPresensi = max($totalPegawai - $hadirHariIni, 0);

        // 🔥 VALIDASI IZIN (AbsenceDocument) - Filter by department for Admins
        $izinPending = AbsenceDocument::where('status', 'pending')
            ->when($admin->department_id, function($q) use ($admin) {
                $q->whereHas('employee', function($eq) use ($admin) {
                    $eq->where('department_id', $admin->department_id);
                });
            })
            ->count();

        // 🔥 VALIDASI KENDALA MESIN (FaultReport)
        $kendalaPending = FaultReport::where('status', 'pending')
            ->count();

        // 🔥 TREND KEHADIRAN 7 HARI TERAKHIR
        $dailyTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $label = Carbon::today()->subDays($i)->format('d M');
            
            $countHadir = AttendanceLog::whereDate('attendance_date', $date)
                ->count();

            $dailyTrends[] = [
                'day' => $label,
                'count' => $countHadir,
            ];
        }

        return [
            'server_time' => now()->format('Y-m-d H:i:s'),
            'today_label' => Carbon::today()->format('Y-m-d'),
            'unit_kerja' => $unitKerja,
            'department_name' => $admin->department?->name ?? $unitKerja ?? 'Unit Kerja',

            // MONITORING
            'total_pegawai' => $totalPegawai,
            'user_aktif' => $userAktif,
            'hadir_hari_ini' => $hadirHariIni,
            'belum_presensi' => $belumPresensi,

            // VALIDASI
            'izin_pending' => $izinPending,
            'kendala_pending' => $kendalaPending,

            // CHART DATA
            'daily_trends' => $dailyTrends,
        ];
    }
}