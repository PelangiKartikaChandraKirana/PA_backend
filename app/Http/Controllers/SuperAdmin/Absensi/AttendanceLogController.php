<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AttendanceLogStatus;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date');
        $q = $request->get('q');
        $statusId = $request->get('status_id');

        $statuses = AttendanceLogStatus::orderBy('name')->get();

        $logs = AttendanceLog::query()
            ->with(['employee','status'])
            ->when($date, fn($qq) => $qq->whereDate('attendance_date',$date))
            ->when($statusId, fn($qq) => $qq->where('status_id',$statusId))
            ->when($q, function ($qq) use ($q) {
                $qq->whereHas('employee', function ($e) use ($q) {
                    $e->where('name','like',"%{$q}%")
                      ->orWhere('nip','like',"%{$q}%");
                });
            })
            ->orderByDesc('attendance_date')
            ->paginate(15)
            ->withQueryString();

        return view(
            'superadmin.absensi.riwayat-presensi.index',
            compact('logs','statuses','date','q','statusId')
        );
    }

    public function show(AttendanceLog $attendance_log)
    {
        $attendance_log->load(['employee','status']);

        return view(
            'superadmin.absensi.riwayat-presensi.show',
            ['item'=>$attendance_log]
        );
    }

    public function export()
    {
        return back()->with('success','Export Excel belum diimplement.');
    }
}
