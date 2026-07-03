<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyActivity;
use App\Models\Employee;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
                'data' => [],
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                'data' => [],
            ], 404);
        }

        $activities = DailyActivity::where('employee_id', $employee->id)
            ->orderByDesc('activity_date')
            ->orderByDesc('start_time')
            ->get();

        return response()->json([
            'message' => 'Laporan kegiatan harian berhasil diambil',
            'data' => $activities->map(function ($item) {
                return [
                    'id' => $item->id,
                    'activity_date' => optional($item->activity_date)->format('Y-m-d'),
                    'start_time' => Carbon::parse($item->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($item->end_time)->format('H:i'),
                    'title' => $item->title,
                    'description' => $item->description,
                    'status' => $item->status,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
            ], 404);
        }

        $request->validate([
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $activityDate = Carbon::parse($request->activity_date);
        $today = now()->startOfDay();

        // Validasi 2: Maksimal 7 hari setelah kegiatan
        $diffDays = $today->diffInDays($activityDate->copy()->startOfDay(), false);
        if ($diffDays < -7) {
            return response()->json([
                'message' => 'Laporan kegiatan maksimal diisi 7 hari setelah pelaksanaan kegiatan.',
            ], 422);
        }

        // Validasi 3: Minggu terakhir bulan berjalan maksimal tanggal 1 bulan berikutnya
        // We will simplify this by ensuring that if the activity is in the previous month, 
        // today's date must be <= 1 of the current month.
        if ($activityDate->month != $today->month) {
            if ($today->day > 1 || $today->month != $activityDate->copy()->addMonth()->month) {
                return response()->json([
                    'message' => 'Kegiatan bulan lalu maksimal diinput pada tanggal 1 bulan berikutnya.',
                ], 422);
            }
        }

        // Validasi 1: Waktu mulai tidak boleh lebih awal dari waktu presensi masuk di hari tersebut
        $attendance = AttendanceLog::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $activityDate->format('Y-m-d'))
            ->first();

        if ($attendance && $attendance->check_in_at) {
            $checkInTime = Carbon::parse($attendance->check_in_at)->format('H:i');
            if ($request->start_time < $checkInTime) {
                return response()->json([
                    'message' => "Waktu mulai kegiatan ({$request->start_time}) tidak boleh lebih awal dari jam masuk Anda ({$checkInTime}).",
                ], 422);
            }
        }

        $activity = DailyActivity::create([
            'employee_id' => $employee->id,
            'activity_date' => $request->activity_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan kegiatan berhasil disimpan',
            'data' => [
                'id' => $activity->id,
                'activity_date' => optional($activity->activity_date)->format('Y-m-d'),
                'start_time' => Carbon::parse($activity->start_time)->format('H:i'),
                'end_time' => Carbon::parse($activity->end_time)->format('H:i'),
                'title' => $activity->title,
                'description' => $activity->description,
                'status' => $activity->status,
            ],
        ], 201);
    }
}
