<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceWeeklySchedule;
use App\Models\AttendanceWeeklyScheduleCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceScheduleController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');

        $categories = AttendanceWeeklyScheduleCategory::orderBy('priority')->get();
        $schedules = AttendanceWeeklySchedule::with('category')
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('category_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'message' => 'Jadwal absensi berhasil diambil',
            'categories' => $categories,
            'schedules' => $schedules,
        ]);
    }

    public function show(AttendanceWeeklySchedule $attendanceSchedule)
    {
        $attendanceSchedule->load('category');

        return response()->json([
            'message' => 'Detail jadwal absensi berhasil diambil',
            'schedule' => $attendanceSchedule,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:attendance_weekly_schedule_categories,id'],
            'day_of_week' => [
                'required',
                'integer',
                'min:1',
                'max:7',
                Rule::unique('attendance_weekly_schedules')
                    ->where(fn ($query) => $query->where('category_id', $request->get('category_id'))),
            ],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s'],
            'tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'effective_minutes' => ['nullable', 'integer', 'min:0'],
            'employee_type' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['tolerance_minutes'] = $data['tolerance_minutes'] ?? 0;
        $data['effective_minutes'] = $data['effective_minutes'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $schedule = AttendanceWeeklySchedule::create($data);

        return response()->json([
            'message' => 'Jadwal absensi berhasil dibuat',
            'schedule' => $schedule,
        ], 201);
    }

    public function update(Request $request, AttendanceWeeklySchedule $attendanceSchedule)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:attendance_weekly_schedule_categories,id'],
            'day_of_week' => [
                'required',
                'integer',
                'min:1',
                'max:7',
                Rule::unique('attendance_weekly_schedules')
                    ->where(fn ($query) => $query->where('category_id', $request->get('category_id')))
                    ->ignore($attendanceSchedule->id),
            ],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s'],
            'tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'effective_minutes' => ['nullable', 'integer', 'min:0'],
            'employee_type' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['tolerance_minutes'] = $data['tolerance_minutes'] ?? 0;
        $data['effective_minutes'] = $data['effective_minutes'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $attendanceSchedule->update($data);

        return response()->json([
            'message' => 'Jadwal absensi berhasil diupdate',
            'schedule' => $attendanceSchedule,
        ]);
    }

    public function destroy(AttendanceWeeklySchedule $attendanceSchedule)
    {
        $attendanceSchedule->delete();

        return response()->json([
            'message' => 'Jadwal absensi berhasil dihapus',
        ]);
    }
}
