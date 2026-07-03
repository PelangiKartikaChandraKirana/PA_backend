<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\MachineFault;
use App\Models\MachineFaultType;
use App\Models\MachineFaultStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MachineFaultController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $typeId = $request->get('type_id');
        $statusId = $request->get('status_id');

        $types = MachineFaultType::orderBy('name')->get();
        $statuses = MachineFaultStatus::orderBy('name')->get();

        $items = MachineFault::query()
            ->with(['type', 'status', 'user'])
            ->when($q, fn($qq) => $qq->where('description', 'like', "%{$q}%"))
            ->when($typeId, fn($qq) => $qq->where('machine_fault_type_id', $typeId))
            ->when($statusId, fn($qq) => $qq->where('machine_fault_status_id', $statusId))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.absensi.lapor-kendala-absensi.index', compact(
            'items','types','statuses','q','typeId','statusId'
        ));
    }

    public function create()
    {
        $types = MachineFaultType::orderBy('name')->get();
        return view('superadmin.absensi.lapor-kendala-absensi.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'machine_fault_type_id' => ['required','exists:machine_fault_types,id'],
            'incident_date' => ['required','date'],
            'description' => ['nullable','string'],
            'evidence' => ['nullable','image','max:4096'],
        ]);

        // default status: cari "proses" kalau ada, kalau tidak ambil status pertama
        $status = MachineFaultStatus::where('key', 'proses')->first()
            ?? MachineFaultStatus::orderBy('id')->first();

        $path = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('machine_faults', 'public');
        }

        MachineFault::create([
            'user_id' => auth()->id(),
            'machine_fault_type_id' => $data['machine_fault_type_id'],
            'machine_fault_status_id' => $status?->id,
            'incident_date' => $data['incident_date'],
            'description' => $data['description'] ?? null,
            'evidence_path' => $path,
        ]);

        return redirect()
            ->route('superadmin.absensi.lapor-kendala-absensi.index')
            ->with('success', 'Laporan kendala berhasil dibuat.');
    }

    public function edit(MachineFault $lapor_kendala_absensi)
    {
        $item = $lapor_kendala_absensi;
        $types = MachineFaultType::orderBy('name')->get();
        $statuses = MachineFaultStatus::orderBy('name')->get();

        return view('superadmin.absensi.lapor-kendala-absensi.edit', compact('item','types','statuses'));
    }

    public function update(Request $request, MachineFault $lapor_kendala_absensi)
    {
        $data = $request->validate([
            'machine_fault_type_id' => ['required','exists:machine_fault_types,id'],
            'machine_fault_status_id' => ['required','exists:machine_fault_statuses,id'],
            'incident_date' => ['required','date'],
            'description' => ['nullable','string'],
            'evidence' => ['nullable','image','max:4096'],
        ]);

        $item = $lapor_kendala_absensi;

        if ($request->hasFile('evidence')) {
            if ($item->evidence_path) {
                Storage::disk('public')->delete($item->evidence_path);
            }
            $item->evidence_path = $request->file('evidence')->store('machine_faults', 'public');
        }

        $item->update([
            'machine_fault_type_id' => $data['machine_fault_type_id'],
            'machine_fault_status_id' => $data['machine_fault_status_id'],
            'incident_date' => $data['incident_date'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('superadmin.absensi.lapor-kendala-absensi.index')
            ->with('success', 'Laporan kendala berhasil diupdate.');
    }

    public function destroy(MachineFault $lapor_kendala_absensi)
    {
        if ($lapor_kendala_absensi->evidence_path) {
            Storage::disk('public')->delete($lapor_kendala_absensi->evidence_path);
        }

        $lapor_kendala_absensi->delete();

        return back()->with('success', 'Laporan kendala berhasil dihapus.');
    }
}