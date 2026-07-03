<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AttendanceWeeklySchedule;
use App\Models\AttendanceWeeklyScheduleCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WeeklyScheduleController extends Controller
{
    public function index(Request $request)
    {
        $categories = AttendanceWeeklyScheduleCategory::orderBy('priority')
            ->orderBy('name')
            ->get();

        $categoryId = $request->get('category_id') ?? optional($categories->first())->id;

        $items = AttendanceWeeklySchedule::when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('superadmin.absensi.jadwal-kerja.index', compact('items', 'categories', 'categoryId'));
    }

    public function create(Request $request)
    {
        $categories = AttendanceWeeklyScheduleCategory::orderBy('priority')
            ->orderBy('name')
            ->get();

        $categoryId = $request->get('category_id') ?? optional($categories->first())->id;

        return view('superadmin.absensi.jadwal-kerja.create', compact('categories', 'categoryId'));
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
                Rule::unique('attendance_weekly_schedules', 'day_of_week')
                    ->where(fn ($query) => $query->where('category_id', $request->input('category_id'))),
            ],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'effective_minutes' => ['nullable', 'integer', 'min:0'],
            'employee_type' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable'],
        ], [
            'day_of_week.unique' => 'Hari kerja untuk kategori ini sudah ada. Silakan pilih hari lain atau ubah data yang sudah ada.',
        ]);

        $data['tolerance_minutes'] = $data['tolerance_minutes'] ?? 0;
        $data['effective_minutes'] = $data['effective_minutes'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        AttendanceWeeklySchedule::create($data);

        return redirect()
            ->route('superadmin.absensi.jadwal-kerja.index', ['category_id' => $data['category_id']])
            ->with('success', 'Jadwal kerja berhasil ditambahkan.');
    }

    public function edit(AttendanceWeeklySchedule $jadwal_kerja)
    {
        $categories = AttendanceWeeklyScheduleCategory::orderBy('priority')
            ->orderBy('name')
            ->get();

        $item = $jadwal_kerja;

        return view('superadmin.absensi.jadwal-kerja.edit', compact('item', 'categories'));
    }

    public function update(Request $request, AttendanceWeeklySchedule $jadwal_kerja)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:attendance_weekly_schedule_categories,id'],
            'day_of_week' => [
                'required',
                'integer',
                'min:1',
                'max:7',
                Rule::unique('attendance_weekly_schedules', 'day_of_week')
                    ->where(fn ($query) => $query->where('category_id', $request->input('category_id')))
                    ->ignore($jadwal_kerja->id),
            ],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'effective_minutes' => ['nullable', 'integer', 'min:0'],
            'employee_type' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable'],
        ], [
            'day_of_week.unique' => 'Hari kerja untuk kategori ini sudah ada. Silakan pilih hari lain atau ubah data yang sudah ada.',
        ]);

        $data['tolerance_minutes'] = $data['tolerance_minutes'] ?? 0;
        $data['effective_minutes'] = $data['effective_minutes'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $jadwal_kerja->update($data);

        return redirect()
            ->route('superadmin.absensi.jadwal-kerja.index', ['category_id' => $data['category_id']])
            ->with('success', 'Jadwal kerja berhasil diupdate.');
    }

    public function destroy(AttendanceWeeklySchedule $jadwal_kerja)
    {
        $categoryId = $jadwal_kerja->category_id;

        $jadwal_kerja->delete();

        return redirect()
            ->route('superadmin.absensi.jadwal-kerja.index', ['category_id' => $categoryId])
            ->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}