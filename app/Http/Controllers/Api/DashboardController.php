<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\AbsenceDocument;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Dashboard berhasil diambil',
            'user' => $request->user(),
            'total_pegawai' => Employee::count(),
            'total_user' => User::count(),
        ]);
    }

    public function tppPercentage(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan',
                'data' => [
                    'total_hadir' => 0,
                    'total_late' => 0,
                    'total_alpha' => 0,
                    'total_tpp' => 0,
                    'total_potongan' => 0,
                    'hadir_percent' => 0,
                    'pengurangan_percent' => 0,
                ]
            ]);
        }

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $today = $now->copy()->endOfDay(); // Check up to today

        $totalWorkingDaysInMonth = 0;
        $totalHadir = 0;
        $totalLate = 0;
        $totalAlpha = 0;
        $reductionPercent = 0.0;

        for ($d = 1; $d <= $now->daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d);
            
            if ($date->isWeekend()) {
                continue;
            }
            $totalWorkingDaysInMonth++;

            if ($date->greaterThan($today)) {
                continue; // Do not check future days
            }

            // Check approved document (Cuti/Izin/Sakit/DL)
            $hasApprovedDoc = AbsenceDocument::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $date->format('Y-m-d'))
                ->whereDate('end_date', '>=', $date->format('Y-m-d'))
                ->exists();

            if ($hasApprovedDoc) {
                // Skip deduction for approved leave
                continue;
            }

            // Check attendance log
            $log = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('attendance_date', $date->format('Y-m-d'))
                ->first();

            $isMonday = $date->isMonday();
            $isFriday = $date->isFriday();

            if (!$log || !$log->check_in_at) {
                $totalAlpha++;
                if ($isMonday) {
                    $reductionPercent += 5; // 3% alpha + 2% no apel
                } else {
                    $reductionPercent += 3; // 3% alpha
                }
                continue; // Cannot be late or PSW if alpha
            }

            $totalHadir++;

            // Apel check for Monday
            if ($isMonday && !$log->apel_at) {
                $reductionPercent += 2;
            }

            // Late (TL) check
            $checkInTime = Carbon::parse($log->check_in_at);
            $limitCheckIn = $date->copy()->setTime(7, 30, 0);
            
            if ($checkInTime->greaterThan($limitCheckIn)) {
                $totalLate++;
                $lateDiffMinutes = $limitCheckIn->diffInMinutes($checkInTime);
                if ($lateDiffMinutes >= 1 && $lateDiffMinutes <= 30) {
                    $reductionPercent += 0.5;
                } elseif ($lateDiffMinutes >= 31 && $lateDiffMinutes <= 60) {
                    $reductionPercent += 1;
                } elseif ($lateDiffMinutes >= 61 && $lateDiffMinutes <= 90) {
                    $reductionPercent += 1.25;
                } elseif ($lateDiffMinutes > 90) {
                    $reductionPercent += 1.5;
                }
            }

            // Early checkout (PSW) check
            if (!$log->check_out_at) {
                $reductionPercent += 1.55;
            } else {
                $checkOutTime = Carbon::parse($log->check_out_at);
                $limitCheckOut = $isFriday 
                    ? $date->copy()->setTime(15, 0, 0) 
                    : $date->copy()->setTime(15, 30, 0);
                
                if ($checkOutTime->lessThan($limitCheckOut)) {
                    $pswDiffMinutes = $checkOutTime->diffInMinutes($limitCheckOut);
                    if ($pswDiffMinutes >= 1 && $pswDiffMinutes <= 30) {
                        $reductionPercent += 0.5;
                    } elseif ($pswDiffMinutes >= 31 && $pswDiffMinutes <= 60) {
                        $reductionPercent += 1;
                    } elseif ($pswDiffMinutes >= 61 && $pswDiffMinutes <= 90) {
                        $reductionPercent += 1.25;
                    } elseif ($pswDiffMinutes > 90) {
                        $reductionPercent += 1.55;
                    }
                }
            }
        }

        if ($reductionPercent > 100) {
            $reductionPercent = 100;
        }

        $tppBase = $employee->tpp_allowance ?? 2000000;
        $totalPotongan = ($tppBase * $reductionPercent) / 100;
        $totalTpp = max(0, $tppBase - $totalPotongan);

        $hadirPercent = $totalWorkingDaysInMonth > 0 
            ? min(100, ($totalHadir / $totalWorkingDaysInMonth) * 100) 
            : 0;

        return response()->json([
            'message' => 'Data TPP berhasil diambil',
            'data' => [
                'total_hadir' => $totalHadir,
                'total_late' => $totalLate,
                'total_alpha' => $totalAlpha,
                'total_tpp' => (int)$totalTpp,
                'total_potongan' => (int)$totalPotongan,
                'hadir_percent' => round($hadirPercent, 2),
                'pengurangan_percent' => round($reductionPercent, 2),
            ]
        ]);
    }
}