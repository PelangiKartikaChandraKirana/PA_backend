<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\AttendanceLog;
use App\Models\Holiday;
use App\Models\AttendanceWeeklySchedule;
use App\Models\AbsenceDocument;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanTppController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        
        $employees = Employee::with('employeeType')->orderBy('name')->get();
        
        // Data pendukung
        $holidays = Holiday::whereMonth('date', $bulan)->whereYear('date', $tahun)->pluck('date')->toArray();
        $holidayStrings = array_map(function($date) {
            return Carbon::parse($date)->toDateString();
        }, $holidays);

        $allLogs = AttendanceLog::whereMonth('attendance_date', $bulan)->whereYear('attendance_date', $tahun)->get()->groupBy('employee_id');
        
        $allDocs = AbsenceDocument::where('status', 'approved')
            ->where(function ($q) use ($bulan, $tahun) {
                $q->whereYear('start_date', $tahun)
                  ->whereMonth('start_date', '<=', $bulan)
                  ->whereYear('end_date', $tahun)
                  ->whereMonth('end_date', '>=', $bulan);
            })->get()->groupBy('employee_id');

        $schedules = AttendanceWeeklySchedule::where('is_active', true)->get();
        
        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        
        $tppData = [];
        
        foreach ($employees as $employee) {
            $eLogs = $allLogs->get($employee->id, collect());
            $eDocs = $allDocs->get($employee->id, collect());
            
            $statHadir = 0;
            $statTerlambatMax30 = 0;
            $statTerlambatLebih30 = 0;
            $statPulangCepatMax30 = 0;
            $statPulangCepatLebih30 = 0;
            $statLupaAbsen = 0;
            $statTK = 0;
            $statIzin = 0;
            
            $totalHariEfektif = 0;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $currentDate = Carbon::create($tahun, $bulan, $d);
                $dateString = $currentDate->toDateString();
                
                // Lewati akhir pekan dan libur nasional
                if ($currentDate->isWeekend() || in_array($dateString, $holidayStrings)) {
                    continue; // Bebas dari hitungan kewajiban
                }
                
                $totalHariEfektif++;
                
                // Cari dokumen (Sakit/Cuti/Izin)
                $isExcused = false;
                foreach ($eDocs as $doc) {
                    if ($currentDate->between($doc->start_date, $doc->end_date)) {
                        $isExcused = true;
                        break;
                    }
                }
                
                if ($isExcused) {
                    $statIzin++;
                    continue; // dibebaskan dari potongan hari ini
                }
                
                $logHariIni = $eLogs->first(function($item) use ($dateString) {
                    return Carbon::parse($item->attendance_date)->toDateString() == $dateString;
                });
                
                if (!$logHariIni) {
                    // Tidak ada log absen sama sekali
                    $statTK++;
                    continue;
                }
                
                $statHadir++;
                
                // Cek kurang absen
                if (!$logHariIni->check_in_at || !$logHariIni->check_out_at) {
                    $statLupaAbsen++;
                    continue; // Jika lupa salah satu, potong khusus 1.5% - lalu stop checker
                }
                
                // Evaluasi Jam
                $dayOfWeek = $currentDate->dayOfWeekIso;
                $empType = $employee->employeeType ? $employee->employeeType->name : null;
                
                // Cari Jadwal
                $sched = null;
                if ($empType) {
                    $sched = $schedules->where('employee_type', $empType)->where('day_of_week', $dayOfWeek)->first();
                }
                if (!$sched) {
                    $sched = $schedules->where('employee_type', null)->where('day_of_week', $dayOfWeek)->first();
                }
                
                // Default fallback jika tidak ada config sama sekali di db
                $targetIn = $sched ? $sched->start_time : '07:30:00';
                $targetOut = $sched ? $sched->end_time : ($dayOfWeek == 5 ? '15:30:00' : '16:00:00');
                $tolerance = $sched ? $sched->tolerance_minutes : 0;
                
                $actualIn = Carbon::parse($logHariIni->check_in_at);
                $limitIn = Carbon::parse($targetIn)->addMinutes($tolerance);
                
                if ($actualIn->greaterThan($limitIn)) {
                    $diffMins = $actualIn->diffInMinutes($limitIn);
                    if ($diffMins <= 30) {
                        $statTerlambatMax30++;
                    } else {
                        $statTerlambatLebih30++;
                    }
                }
                
                $actualOut = Carbon::parse($logHariIni->check_out_at);
                $limitOut = Carbon::parse($targetOut);
                
                if ($actualOut->lessThan($limitOut)) {
                    $diffMinsOut = $limitOut->diffInMinutes($actualOut);
                    if ($diffMinsOut <= 30) {
                        $statPulangCepatMax30++;
                    } else {
                        $statPulangCepatLebih30++;
                    }
                }
            }
            
            // Perhitungan Persen Potongan (Aturan Baru)
            $penguranganPercent = 0.0;
            $penguranganPercent += ($statTK * 3.0);
            $penguranganPercent += ($statLupaAbsen * 1.5);
            $penguranganPercent += ($statTerlambatMax30 * 0.5);
            $penguranganPercent += ($statTerlambatLebih30 * 1.0);
            $penguranganPercent += ($statPulangCepatMax30 * 0.5);
            $penguranganPercent += ($statPulangCepatLebih30 * 1.0);
            
            if ($penguranganPercent > 100) $penguranganPercent = 100;
            
            $tppAllowance = !empty($employee->tpp_allowance) ? $employee->tpp_allowance : 5000000;
            $totalPotongan = round(($penguranganPercent / 100) * $tppAllowance);
            $tppDiterima = $tppAllowance - $totalPotongan;
            if ($tppDiterima < 0) $tppDiterima = 0;
            
            $tppData[] = (object) [
                'employee' => $employee,
                'totalHariEfektif' => $totalHariEfektif,
                'statHadir' => $statHadir,
                'statTK' => $statTK,
                'statIzin' => $statIzin,
                'statLupaAbsen' => $statLupaAbsen,
                'statTerlambatMax30' => $statTerlambatMax30,
                'statTerlambatLebih30' => $statTerlambatLebih30,
                'statPulangCepatMax30' => $statPulangCepatMax30,
                'statPulangCepatLebih30' => $statPulangCepatLebih30,
                'penguranganPercent' => $penguranganPercent,
                'totalPotongan' => $totalPotongan,
                'tppAllowance' => $tppAllowance,
                'tppDiterima' => $tppDiterima,
            ];
        }

        return view('superadmin.laporan.tpp', compact('tppData', 'bulan', 'tahun'));
    }
}
