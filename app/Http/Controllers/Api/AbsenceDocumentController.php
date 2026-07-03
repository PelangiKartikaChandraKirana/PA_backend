<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\AbsenceDocument;
use App\Notifications\AbsenceRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class AbsenceDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
                'data' => [],
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                'data' => [],
            ], 404);
        }

        $documents = AbsenceDocument::where('employee_id', $employee->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        // Fetch all active document types for lookup
        $docTypes = \App\Models\DocumentType::all()->keyBy('name');

        return response()->json([
            'message' => 'Dokumen ketidakhadiran berhasil diambil',
            'data' => $documents->map(function ($item) use ($docTypes) {
                $typeInfo = $docTypes->get($item->document_type);
                return [
                    'id' => $item->id,
                    'document_type' => $item->document_type,
                    'color' => $typeInfo ? $typeInfo->color : '#3b82f6',
                    'is_required' => $typeInfo ? (bool)$typeInfo->is_required : true, // Default true for safety
                    'title' => $item->title,
                    'file_path' => $item->file_path,
                    'file_url' => $item->file_path ? asset('storage/' . $item->file_path) : null,
                    'start_date' => optional($item->start_date)->format('Y-m-d'),
                    'end_date' => optional($item->end_date)->format('Y-m-d'),
                    'status' => $item->status,
                    'approved_by' => $item->approved_by,
                    'rejected_by' => $item->rejected_by,
                    'decision_notes' => $item->decision_notes,
                    'decided_at' => optional($item->decided_at)->format('Y-m-d H:i:s'),
                    'notes' => $item->notes,
                    'created_at' => optional($item->created_at)->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
            ], 404);
        }

        $request->validate([
            'document_type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'lokasi_tujuan' => 'nullable|string|max:255',
            'nama_kegiatan' => 'nullable|string|max:255',
        ]);

        $startDate = $request->date('start_date');
        $endDate = $request->date('end_date');

        // Validation: Maximum 3 days after start_date
        $today = now()->startOfDay();
        $diffDays = $today->diffInDays($startDate->startOfDay(), false);
        if ($diffDays < -3) {
            return response()->json([
                'message' => 'Surat keterangan wajib diunggah maksimal 3 hari setelah kegiatan dilaksanakan.',
            ], 422);
        }

        if (strtolower($request->document_type) === 'dinas luar') {
            if (empty($request->lokasi_tujuan) || empty($request->nama_kegiatan)) {
                return response()->json([
                    'message' => 'Tempat/Lokasi Tujuan dan Nama Kegiatan wajib diisi untuk Dinas Luar.',
                ], 422);
            }
        }

        $hasOverlap = AbsenceDocument::where('employee_id', $employee->id)
            ->where('status', '!=', 'rejected')
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();

        if ($hasOverlap) {
            return response()->json([
                'message' => 'Pengajuan pada rentang tanggal tersebut sudah ada dan masih aktif.',
            ], 422);
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('absence_documents', 'public');
        }

        $document = AbsenceDocument::create([
            'employee_id' => $employee->id,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'file_path' => $filePath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'lokasi_tujuan' => $request->lokasi_tujuan,
            'nama_kegiatan' => $request->nama_kegiatan,
            'status' => 'pending',
            'approved_by' => null,
            'rejected_by' => null,
            'decision_notes' => null,
            'decided_at' => null,
            'notes' => $request->notes,
        ]);

        // --- TRIGGER NOTIFIKASI ---
        try {
            // Ambil semua Superadmin
            $superAdmins = User::where('role', 'superadmin')->get();
            
            // Ambil Admin OPD yang sama dengan pegawai
            $departmentAdmins = collect();
            if ($employee->department_id) {
                $departmentAdmins = User::where('role', 'admin')
                    ->where('department_id', $employee->department_id)
                    ->get();
            }

            $recipients = $superAdmins->concat($departmentAdmins)->unique('id');
            
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new AbsenceRequestNotification($document, $employee));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi: ' . $e->getMessage());
        }
        // --- END TRIGGER ---

        return response()->json([
            'message' => 'Dokumen ketidakhadiran berhasil dikirim',
            'data' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'title' => $document->title,
                'file_path' => $document->file_path,
                'file_url' => $document->file_path ? asset('storage/' . $document->file_path) : null,
                'start_date' => optional($document->start_date)->format('Y-m-d'),
                'end_date' => optional($document->end_date)->format('Y-m-d'),
                'status' => $document->status,
                'approved_by' => $document->approved_by,
                'rejected_by' => $document->rejected_by,
                'decision_notes' => $document->decision_notes,
                'decided_at' => optional($document->decided_at)->format('Y-m-d H:i:s'),
                'notes' => $document->notes,
            ],
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
            ], 404);
        }

        $document = AbsenceDocument::where('employee_id', $employee->id)
            ->where('id', $id)
            ->first();

        if (!$document) {
            return response()->json([
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail dokumen ketidakhadiran berhasil diambil',
            'data' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'title' => $document->title,
                'file_path' => $document->file_path,
                'file_url' => $document->file_path ? asset('storage/' . $document->file_path) : null,
                'start_date' => optional($document->start_date)->format('Y-m-d'),
                'end_date' => optional($document->end_date)->format('Y-m-d'),
                'status' => $document->status,
                'approved_by' => $document->approved_by,
                'rejected_by' => $document->rejected_by,
                'decision_notes' => $document->decision_notes,
                'decided_at' => optional($document->decided_at)->format('Y-m-d H:i:s'),
                'notes' => $document->notes,
                'created_at' => optional($document->created_at)->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}