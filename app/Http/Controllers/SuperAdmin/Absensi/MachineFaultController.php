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

        // Statistik Dashboard
        $stats = [
            'total' => MachineFault::count(),
            'pending' => MachineFault::whereHas('machineFaultStatus', function($s) {
                $s->where('name', 'like', '%Pending%')->orWhere('name', 'like', '%Baru%');
            })->count(),
            'resolved' => MachineFault::whereHas('machineFaultStatus', function($s) {
                $s->where('name', 'like', '%Selesai%')->orWhere('name', 'like', '%Resolved%');
            })->count(),
        ];

        $items = MachineFault::query()
            ->with(['machineFaultType', 'machineFaultStatus', 'employee.department'])
            ->when($q, function ($qq) use ($q) {
                $qq->where('description', 'like', "%{$q}%");
            })
            ->when($typeId, fn ($qq) => $qq->where('machine_fault_type_id', $typeId))
            ->when($statusId, fn ($qq) => $qq->where('machine_fault_status_id', $statusId))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.absensi.lapor-kendala-absensi.index', compact(
            'items', 'types', 'statuses', 'q', 'typeId', 'statusId', 'stats'
        ));
    }

    public function create()
    {
        $types = MachineFaultType::orderBy('name')->get();
        $statuses = MachineFaultStatus::orderBy('name')->get();

        return view('superadmin.absensi.lapor-kendala-absensi.create', compact('types', 'statuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'machine_fault_type_id' => ['required', 'exists:machine_fault_types,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'machine_fault_status_id' => ['nullable', 'exists:machine_fault_statuses,id'],
            'evidence' => ['nullable', 'image', 'max:4096'],
        ]);

        // default status: ambil status pertama (atau kamu bisa bikin "Proses" id tertentu)
        if (empty($data['machine_fault_status_id'])) {
            $data['machine_fault_status_id'] = MachineFaultStatus::orderBy('id')->value('id');
        }

        if ($request->hasFile('evidence')) {
            $data['evidence_path'] = $request->file('evidence')->store('machine_faults', 'public');
        }


        $fault = MachineFault::create($data);

        // Kirim notifikasi ke user terkait jika ada employee_id
        if ($fault->employee_id) {
            $employee = $fault->employee;
            if ($employee && $employee->user) {
                $employee->user->notify(new \App\Notifications\MachineFaultReported($fault));
            }
        }

        return redirect()
            ->route('superadmin.absensi.lapor-kendala-absensi.index')
            ->with('success', 'Laporan kendala berhasil ditambahkan.');
    }

    public function edit(MachineFault $lapor_kendala_absensi)
    {
        $item = $lapor_kendala_absensi;
        $types = MachineFaultType::orderBy('name')->get();
        $statuses = MachineFaultStatus::orderBy('name')->get();

        return view('superadmin.absensi.lapor-kendala-absensi.edit', compact('item', 'types', 'statuses'));
    }

    public function update(Request $request, MachineFault $lapor_kendala_absensi)
    {
        $item = $lapor_kendala_absensi;

        $data = $request->validate([
            'machine_fault_type_id' => ['required', 'exists:machine_fault_types,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'machine_fault_status_id' => ['required', 'exists:machine_fault_statuses,id'],
            'evidence' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('evidence')) {
            // hapus lama
            if ($item->evidence_path) {
                Storage::disk('public')->delete($item->evidence_path);
            }
            $data['evidence_path'] = $request->file('evidence')->store('machine_faults', 'public');
        }

        $item->update($data);

        return redirect()
            ->route('superadmin.absensi.lapor-kendala-absensi.index')
            ->with('success', 'Laporan kendala berhasil diupdate.');
    }

    public function destroy(MachineFault $lapor_kendala_absensi)
    {
        $item = $lapor_kendala_absensi;

        if ($item->evidence_path) {
            Storage::disk('public')->delete($item->evidence_path);
        }

        $item->delete();

        return back()->with('success', 'Laporan kendala berhasil dihapus.');
    }
}