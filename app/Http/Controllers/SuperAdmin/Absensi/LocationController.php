<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->get('unit_id');
        $search = $request->get('q');

        // kalau kamu belum punya tabel departments, bisa comment baris ini dan dropdown unit jadi kosong
        $units = class_exists(Department::class)
            ? Department::orderBy('name')->get(['id','name'])
            : collect();

        $items = Location::query()
            ->when($unitId, fn ($q) => $q->where('unit_id', $unitId))
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.absensi.lokasi-absen.index', compact(
            'items','units','unitId','search'
        ));
    }

    public function create()
    {
        $units = class_exists(Department::class)
            ? Department::orderBy('name')->get(['id','name'])
            : collect();

        return view('superadmin.absensi.lokasi-absen.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_id' => ['nullable','integer'],
            'name' => ['required','string','max:255'],
            'latitude' => ['required','numeric','between:-90,90'],
            'longitude' => ['required','numeric','between:-180,180'],
            'radius_meters' => ['required','integer','min:0'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Location::create($data);

        return redirect()
            ->route('superadmin.absensi.lokasi-absen.index')
            ->with('success', 'Lokasi absen berhasil ditambahkan.');
    }

    public function edit(Location $lokasi_absen)
    {
        $item = $lokasi_absen;

        $units = class_exists(Department::class)
            ? Department::orderBy('name')->get(['id','name'])
            : collect();

        return view('superadmin.absensi.lokasi-absen.edit', compact('item','units'));
    }

    public function update(Request $request, Location $lokasi_absen)
    {
        $data = $request->validate([
            'unit_id' => ['nullable','integer'],
            'name' => ['required','string','max:255'],
            'latitude' => ['required','numeric','between:-90,90'],
            'longitude' => ['required','numeric','between:-180,180'],
            'radius_meters' => ['required','integer','min:0'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $lokasi_absen->update($data);

        return redirect()
            ->route('superadmin.absensi.lokasi-absen.index')
            ->with('success', 'Lokasi absen berhasil diupdate.');
    }

    public function destroy(Location $lokasi_absen)
    {
        $lokasi_absen->delete();

        return back()->with('success', 'Lokasi absen berhasil dihapus.');
    }
}