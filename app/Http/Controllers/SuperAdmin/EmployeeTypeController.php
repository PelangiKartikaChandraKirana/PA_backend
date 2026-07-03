<?php

namespace App\Http\Controllers\SuperAdmin\Master;

use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use Illuminate\Http\Request;

class EmployeeTypeController extends Controller
{
    public function index()
    {
        $types = EmployeeType::orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('superadmin.tipe-pegawai.index', compact('types'));
    }

    public function create()
    {
        return view('superadmin.tipe-pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $validated['is_honorarium'] = $request->has('is_honorarium');
        $validated['is_active'] = $request->has('is_active');

        EmployeeType::create($validated);

        return redirect()
            ->route('superadmin.tipe-pegawai.index')
            ->with('success', 'Tipe pegawai berhasil ditambahkan.');
    }

    public function edit(EmployeeType $tipe_pegawai)
    {
        return view('superadmin.tipe-pegawai.edit', [
            'employeeType' => $tipe_pegawai
        ]);
    }

    public function update(Request $request, EmployeeType $tipe_pegawai)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $validated['is_honorarium'] = $request->has('is_honorarium');
        $validated['is_active'] = $request->has('is_active');

        $tipe_pegawai->update($validated);

        return redirect()
            ->route('superadmin.tipe-pegawai.index')
            ->with('success', 'Tipe pegawai berhasil diperbarui.');
    }

    public function destroy(EmployeeType $tipe_pegawai)
    {
        $tipe_pegawai->delete();

        return redirect()
            ->route('superadmin.tipe-pegawai.index')
            ->with('success', 'Tipe pegawai berhasil dihapus.');
    }
}