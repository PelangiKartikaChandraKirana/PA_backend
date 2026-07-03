<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceDocument;
use App\Models\FaultReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VerificationController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        $unitKerja = $admin->unit_kerja;

        $documents = AbsenceDocument::with('employee')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.verifikasi.index', compact('documents', 'unitKerja'));
    }

    public function kendala()
    {
        $admin = auth()->user();
        $unitKerja = $admin->unit_kerja;

        $reports = FaultReport::with('employee')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.verifikasi.kendala', compact('reports', 'unitKerja'));
    }

    public function approveIzin(Request $request, AbsenceDocument $document)
    {
        $admin = auth()->user();
        
        $document->update([
            'status' => 'approved',
            'approved_by' => $admin->name,
            'decided_at' => now(),
            'decision_notes' => $request->notes,
        ]);

        return back()->with('success', 'Dokumen pengajuan berhasil disetujui.');
    }

    public function rejectIzin(Request $request, AbsenceDocument $document)
    {
        $admin = auth()->user();

        $document->update([
            'status' => 'rejected',
            'rejected_by' => $admin->name,
            'decided_at' => now(),
            'decision_notes' => $request->notes,
        ]);

        return back()->with('error', 'Dokumen pengajuan telah ditolak.');
    }

    public function approveKendala(Request $request, FaultReport $report)
    {
        $admin = auth()->user();

        $report->update([
            'status' => 'approved',
            'handled_by' => $admin->name,
        ]);

        return back()->with('success', 'Laporan kendala berhasil divalidasi.');
    }

    public function rejectKendala(Request $request, FaultReport $report)
    {
        $admin = auth()->user();

        $report->update([
            'status' => 'rejected',
            'handled_by' => $admin->name,
        ]);

        return back()->with('error', 'Laporan kendala ditolak/tidak valid.');
    }
}
