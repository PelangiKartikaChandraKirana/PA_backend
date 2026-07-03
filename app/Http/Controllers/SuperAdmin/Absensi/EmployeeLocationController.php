<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;

class EmployeeLocationController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->get('unit_id');
        $search = $request->get('q'); // ✅ ini yang dipakai di blade: $search

        // List OPD/Unit (Department)
        $units = Department::orderBy('name')->get();

        // List pegawai + hitung jumlah lokasi override aktif
        $employees = Employee::query()
            ->withCount([
                'locations as override_locations_count' => fn ($q) => $q->where('employee_locations.is_active', true)
            ])
            ->when($unitId, fn ($q) => $q->where('department_id', $unitId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // List lokasi (bisa ikut filter unit kalau kamu mau)
        $locations = Location::query()
            ->when($unitId, fn ($q) => $q->where('unit_id', $unitId))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('superadmin.absensi.lokasi-absen-pegawai.index', compact(
            'units',
            'unitId',
            'search',
            'employees',
            'locations'
        ));
    }

    // JSON detail lokasi override aktif untuk 1 pegawai (buat modal / ajax)
    public function show(Employee $employee)
    {
        $activeIds = $employee->locations()
            ->wherePivot('is_active', true)
            ->pluck('locations.id');

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'nip' => $employee->nip,
            ],
            'active_location_ids' => $activeIds,
        ]);
    }

    // Simpan override lokasi untuk 1 pegawai (replace semua override jadi yang dipilih)
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'location_ids' => ['nullable', 'array'],
            'location_ids.*' => ['integer', 'exists:locations,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $locationIds = $data['location_ids'] ?? [];

        // siapkan pivot data: semua yang dipilih -> is_active true
        $syncData = [];
        foreach ($locationIds as $id) {
            $syncData[$id] = ['is_active' => true];
        }

        // replace seluruh override lokasi pegawai
        $employee->locations()->sync($syncData);

        return back()->with('success', 'Lokasi absen pegawai berhasil disimpan.');
    }

    // Bulk assign lokasi untuk banyak pegawai
    // - replace=true: replace override masing-masing pegawai
    // - replace=false: add tanpa menghapus yang lama
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'location_ids' => ['required', 'array', 'min:1'],
            'location_ids.*' => ['integer', 'exists:locations,id'],
            'replace' => ['nullable'],
        ]);

        $replace = $request->boolean('replace');

        $syncData = [];
        foreach ($data['location_ids'] as $id) {
            $syncData[$id] = ['is_active' => true];
        }

        $employees = Employee::whereIn('id', $data['employee_ids'])->get();

        foreach ($employees as $emp) {
            if ($replace) {
                $emp->locations()->sync($syncData);
            } else {
                $emp->locations()->syncWithoutDetaching($syncData);
            }
        }

        return back()->with('success', 'Bulk set lokasi pegawai berhasil.');
    }
}