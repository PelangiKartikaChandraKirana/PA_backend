<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Department;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->get('unit_id');
        $q = $request->get('q');

        // Stats Dashboard
        $stats = [
            'total' => Machine::count(),
            'online' => Machine::where('is_active', true)
                        ->where('last_seen_at', '>=', now()->subMinutes(5))
                        ->count(),
            'offline' => Machine::where('is_active', true)
                        ->where(function($qq) {
                            $qq->where('last_seen_at', '<', now()->subMinutes(5))
                               ->orWhereNull('last_seen_at');
                        })
                        ->count(),
            'inactive' => Machine::where('is_active', false)->count(),
        ];

        // List OPD/Unit
        $units = class_exists(Department::class) ? Department::orderBy('name')->get() : collect();

        $items = Machine::query()
            ->when($unitId, fn ($qq) => $qq->where('unit_id', $unitId))
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('ip_address', 'like', "%{$q}%")
                      ->orWhere('serial_number', 'like', "%{$q}%")
                      ->orWhere('location_name', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.absensi.mesin.index', compact('items', 'q', 'unitId', 'units', 'stats'));
    }

    public function create()
    {
        $units = class_exists(Department::class) ? Department::orderBy('name')->get() : collect();
        return view('superadmin.absensi.mesin.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:machines,serial_number'],
            'ip_address' => ['nullable', 'string', 'max:50'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Machine::create($data);

        return redirect()->route('superadmin.absensi.mesin.index')
            ->with('success', 'Mesin absensi berhasil didaftarkan.');
    }

    public function edit(Machine $mesin)
    {
        $item = $mesin;
        $units = class_exists(Department::class) ? Department::orderBy('name')->get() : collect();
        return view('superadmin.absensi.mesin.edit', compact('item', 'units'));
    }

    public function update(Request $request, Machine $mesin)
    {
        $data = $request->validate([
            'unit_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:machines,serial_number,' . $mesin->id],
            'ip_address' => ['nullable', 'string', 'max:50'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $mesin->update($data);

        return redirect()->route('superadmin.absensi.mesin.index')
            ->with('success', 'Data mesin berhasil diperbarui.');
    }

    public function destroy(Machine $mesin)
    {
        $mesin->delete();
        return back()->with('success', 'Mesin berhasil dihapus.');
    }
}