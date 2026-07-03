<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AttendanceWeeklyScheduleCategory;
use Illuminate\Http\Request;

class WeeklyScheduleCategoryController extends Controller
{
    public function index()
    {
        $items = AttendanceWeeklyScheduleCategory::orderBy('priority')
            ->orderBy('name')
            ->get();

        return view('superadmin.absensi.kategori-jadwal-kerja.index', compact('items'));
    }

    public function create()
    {
        return view('superadmin.absensi.kategori-jadwal-kerja.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'priority' => ['required', 'integer', 'min:1'],
        ]);

        AttendanceWeeklyScheduleCategory::create($data);

        return redirect()
            ->route('superadmin.absensi.kategori-jadwal-kerja.index')
            ->with('success', 'Kategori jadwal kerja berhasil ditambahkan.');
    }

    public function edit(AttendanceWeeklyScheduleCategory $kategori_jadwal_kerja)
    {
        $item = $kategori_jadwal_kerja;

        return view('superadmin.absensi.kategori-jadwal-kerja.edit', compact('item'));
    }

    public function update(Request $request, AttendanceWeeklyScheduleCategory $kategori_jadwal_kerja)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'priority' => ['required', 'integer', 'min:1'],
        ]);

        $kategori_jadwal_kerja->update($data);

        return redirect()
            ->route('superadmin.absensi.kategori-jadwal-kerja.index')
            ->with('success', 'Kategori jadwal kerja berhasil diupdate.');
    }

    public function destroy(AttendanceWeeklyScheduleCategory $kategori_jadwal_kerja)
    {
        $kategori_jadwal_kerja->delete();

        return redirect()
            ->route('superadmin.absensi.kategori-jadwal-kerja.index')
            ->with('success', 'Kategori jadwal kerja berhasil dihapus.');
    }
}