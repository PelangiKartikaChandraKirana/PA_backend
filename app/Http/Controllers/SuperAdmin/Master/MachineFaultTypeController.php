<?php

namespace App\Http\Controllers\SuperAdmin\Master;

use App\Http\Controllers\Controller;
use App\Models\MachineFaultType;
use Illuminate\Http\Request;

class MachineFaultTypeController extends Controller
{
    public function index()
    {
        $items = MachineFaultType::orderBy('priority')->get();
        return view('superadmin.master.tipe-kendala.index', compact('items'));
    }

    public function create()
    {
        $maxPriority = MachineFaultType::max('priority') ?? 0;
        return view('superadmin.master.tipe-kendala.create', compact('maxPriority'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:machine_fault_types,name'],
            'priority' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        MachineFaultType::create($validated);

        return redirect()
            ->route('superadmin.master.tipe-kendala.index')
            ->with('success', 'Jenis Kendala berhasil ditambahkan.');
    }

    public function edit(MachineFaultType $tipe_kendala)
    {
        return view('superadmin.master.tipe-kendala.edit', compact('tipe_kendala'));
    }

    public function update(Request $request, MachineFaultType $tipe_kendala)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:machine_fault_types,name,' . $tipe_kendala->id],
            'priority' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $tipe_kendala->update($validated);

        return redirect()
            ->route('superadmin.master.tipe-kendala.index')
            ->with('success', 'Jenis Kendala berhasil diperbarui.');
    }

    public function destroy(MachineFaultType $tipe_kendala)
    {
        // Cek jika sudah dipakai di laporan kendala
        if ($tipe_kendala->machineFaults()->exists()) {
            return redirect()
                ->route('superadmin.master.tipe-kendala.index')
                ->with('error', 'Gagal dihapus karena tipe ini sudah digunakan pada pelaporan kendala. Silakan nonaktifkan saja statusnya.');
        }

        $tipe_kendala->delete();

        return redirect()
            ->route('superadmin.master.tipe-kendala.index')
            ->with('success', 'Jenis Kendala berhasil dihapus.');
    }
}
