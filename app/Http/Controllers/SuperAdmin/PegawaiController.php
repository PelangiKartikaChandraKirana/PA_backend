<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeFace;
use App\Models\AbsenceDocument;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserLog;
use App\Models\Position;
use App\Models\Department;
use App\Models\Company;
use App\Models\EmployeeType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PegawaiController extends Controller
{
    // ======================
    // CRUD PEGAWAI
    // ======================

    public function index()
    {
        $employees = Employee::with([
            'position',
            'department',
            'employeeType'
        ])->latest()->get();

        return view('superadmin.pegawai.index', compact('employees'));
    }

    public function create()
    {
        $positions = Position::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $employeeTypes = EmployeeType::orderBy('priority')->get();

        return view('superadmin.pegawai.create', compact('positions', 'departments', 'employeeTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip',
            'name' => 'required|string|max:150',
            'position_id' => 'nullable|exists:positions,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_type_id' => 'nullable|exists:employee_types,id',
            'tpp_allowance' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        Employee::create([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'position_id' => $validated['position_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'employee_type_id' => $validated['employee_type_id'] ?? null,
            'tpp_allowance' => $validated['tpp_allowance'] ?? 0,
            'status' => $validated['status'] ?? 'Aktif',
        ]);

        return redirect()
            ->route('superadmin.pegawai.index')
            ->with('success', 'Data Pegawai berhasil ditambahkan');
    }

    public function show(Employee $pegawai)
    {
        return view('superadmin.pegawai.show', compact('pegawai'));
    }

    public function edit(Employee $pegawai)
    {
        $positions = Position::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $employeeTypes = EmployeeType::orderBy('priority')->get();

        return view('superadmin.pegawai.edit', compact('pegawai', 'positions', 'departments', 'employeeTypes'));
    }

    public function update(Request $request, Employee $pegawai)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip,' . $pegawai->id,
            'name' => 'required|string|max:150',
            'position_id' => 'nullable|exists:positions,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_type_id' => 'nullable|exists:employee_types,id',
            'tpp_allowance' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        $pegawai->update([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'position_id' => $validated['position_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'employee_type_id' => $validated['employee_type_id'] ?? null,
            'tpp_allowance' => $validated['tpp_allowance'] ?? 0,
            'status' => $validated['status'] ?? 'Aktif',
        ]);

        return redirect()
            ->route('superadmin.pegawai.index')
            ->with('success', 'Data Pegawai berhasil diupdate');
    }

    public function destroy(Employee $pegawai)
    {
        $pegawai->delete();

        return redirect()
            ->route('superadmin.pegawai.index')
            ->with('success', 'Data Pegawai berhasil dihapus');
    }

    // ======================
    // MANAJEMEN WAJAH
    // ======================

    public function wajah(Request $request)
    {
        $query = Employee::with('faces');

        if ($request->status === 'registered') {
            $query->has('faces');
        }

        if ($request->status === 'not_registered') {
            $query->doesntHave('faces');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        $employees = $query->latest()->get();

        return view('superadmin.pegawai.wajah', compact('employees'));
    }

    public function storeWajah(Request $request, Employee $pegawai)
    {
        $validated = $request->validate([
            'face_image' => 'required|image|max:2048',
        ]);

        $path = $validated['face_image']->store('faces', 'public');

        EmployeeFace::create([
            'employee_id' => $pegawai->id,
            'image_path' => $path,
            'is_active' => true,
        ]);

        // Sync with Flask face service
        $user = User::where('nip', $pegawai->nip)->first();
        if ($user) {
            try {
                $imageData = file_get_contents($request->file('face_image')->getRealPath());
                $response = Http::attach(
                    'face_image',
                    $imageData,
                    $request->file('face_image')->getClientOriginalName()
                )->post('http://127.0.0.1:5001/register', [
                    'user_id' => $user->id,
                ]);

                if ($response->successful()) {
                    Log::info('Face service sync OK for User ID: ' . $user->id);
                } else {
                    Log::error('Face service sync failed: ' . $response->body());
                    return back()->with('warning', 'Wajah tersimpan di DB, tapi gagal sync ke face service: ' . ($response->json()['message'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                Log::error('Face service connection error: ' . $e->getMessage());
                return back()->with('warning', 'Wajah tersimpan di DB, tapi face service tidak aktif. Jalankan face service lalu sync ulang.');
            }
        } else {
            Log::warning('No user found with NIP: ' . $pegawai->nip . ' — cannot sync to face service');
            return back()->with('warning', 'Wajah tersimpan, tapi tidak ada user dengan NIP ' . $pegawai->nip . ' untuk sync ke face service.');
        }

        return back()->with('success', 'Wajah berhasil ditambahkan dan disinkronkan ke face service');
    }

    public function deleteWajah(EmployeeFace $face)
    {
        if ($face->image_path) {
            Storage::disk('public')->delete($face->image_path);
        }

        $face->delete();

        return back()->with('success', 'Wajah berhasil dihapus');
    }

    public function setAktif(EmployeeFace $face)
    {
        EmployeeFace::where('employee_id', $face->employee_id)
            ->update(['is_active' => false]);

        $face->update(['is_active' => true]);

        return back()->with('success', 'Wajah berhasil diaktifkan');
    }

    /**
     * Sync all active EmployeeFace records to the Flask face service.
     * Re-registers every face encoding so known_faces/*.pkl is up to date.
     */
    public function syncAllWajah()
    {
        $faces = EmployeeFace::where('is_active', true)->with('employee')->get();
        $synced = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($faces as $face) {
            $employee = $face->employee;
            if (!$employee) {
                $skipped++;
                continue;
            }

            $user = User::where('nip', $employee->nip)->first();
            if (!$user) {
                $skipped++;
                Log::warning("syncAllWajah: No user with NIP {$employee->nip}");
                continue;
            }

            $fullPath = Storage::disk('public')->path($face->image_path);
            if (!file_exists($fullPath)) {
                $failed++;
                Log::error("syncAllWajah: File not found — {$face->image_path}");
                continue;
            }

            try {
                $response = Http::attach(
                    'face_image',
                    file_get_contents($fullPath),
                    basename($face->image_path)
                )->post('http://127.0.0.1:5001/register', [
                    'user_id' => $user->id,
                ]);

                if ($response->successful()) {
                    $synced++;
                } else {
                    $failed++;
                    Log::error("syncAllWajah: Flask error for user {$user->id} — " . $response->body());
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("syncAllWajah: Connection error — " . $e->getMessage());
            }
        }

        return back()->with('success', "Sync selesai: {$synced} berhasil, {$failed} gagal, {$skipped} dilewati.");
    }

    // ======================
    // DOKUMEN KETIDAKHADIRAN
    // ======================

    public function ketidakhadiran()
    {
        $documents = AbsenceDocument::with('employee')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $summary = [
            'pending' => $documents->where('status', 'pending')->count(),
            'approved' => $documents->where('status', 'approved')->count(),
            'rejected' => $documents->where('status', 'rejected')->count(),
        ];

        return view('superadmin.pegawai.ketidakhadiran', compact('documents', 'summary'));
    }

    public function updateDocumentStatus(Request $request, AbsenceDocument $document)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'decision_notes' => [
                Rule::requiredIf(fn () => $request->input('status') === 'rejected'),
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $actorName = auth()->user()->name ?? 'Superadmin';
        $status = $validated['status'];

        $document->update([
            'status' => $status,
            'approved_by' => $status === 'approved' ? $actorName : null,
            'rejected_by' => $status === 'rejected' ? $actorName : null,
            'decision_notes' => $status === 'pending'
                ? null
                : ($validated['decision_notes'] ?? null),
            'decided_at' => $status === 'pending' ? null : now(),
        ]);

        return back()->with('success', 'Status dokumen ketidakhadiran berhasil diperbarui');
    }

    // ======================
    // DATA PENGGUNA
    // ======================

    public function pengguna(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('opd')) {
            $query->where('unit_kerja', 'like', '%' . $request->opd . '%');
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('superadmin.pengguna.index', compact('users'));
    }

    public function createPengguna()
    {
        $companies = Company::orderBy('name')->get();

        return view('superadmin.pengguna.create', compact('companies'));
    }

    public function storePengguna(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|max:50',
            'nip' => 'required|string|max:50',
            'unit_kerja' => 'required|string|max:150',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|in:Aktif,Nonaktif',
            'face_image' => 'required|string', // Base64 string
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'unit_kerja' => $validated['unit_kerja'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'status' => $validated['status'] ?? 'Aktif',
        ]);

        // Handing Face Image
        $nipFallback = $validated['nip'] ?? $user->id;

        if ($request->filled('face_image')) {

            try {
                $base64Image = $request->face_image;

                if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    throw new \Exception('face_image format harus data:image/<type>;base64,...');
                }

                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    throw new \Exception('Invalid image type: ' . $type);
                }

                $base64Payload = substr($base64Image, strpos($base64Image, ',') + 1);
                $imageData = base64_decode($base64Payload);
                if ($imageData === false) {
                    throw new \Exception('base64_decode failed');
                }

                $fileName = 'faces/' . $nipFallback . '_' . time() . '.' . $type;
                Storage::disk('public')->put($fileName, $imageData);


                // Find or create Employee
                $employee = Employee::firstOrCreate(
                    ['nip' => $nipFallback],
                    ['name' => $validated['name'], 'status' => 'Aktif']
                );

                // Deactivate old faces
                EmployeeFace::where('employee_id', $employee->id)->update(['is_active' => false]);

                // Store Face
                EmployeeFace::create([
                    'employee_id' => $employee->id,
                    'image_path' => $fileName,
                    'is_active' => true,
                ]);

                // SYNC WITH FACE SERVICE (PYTHON)
                try {
                    $response = Http::attach(
                        'face_image',
                        $imageData,
                        basename($fileName)
                    )->post('http://127.0.0.1:5001/register', [
                        'user_id' => $user->id,
                    ]);

                    if ($response->successful()) {
                        Log::info('Face service sync successful for User ID: ' . $user->id);
                    } else {
                        Log::error('Face service sync failed: ' . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error('Face service connection error: ' . $e->getMessage());
                }

                Log::info('Face image saved successfully for NIP: ' . $nipFallback);

            } catch (\Exception $e) {
                Log::error('Error saving face image in storePengguna: ' . $e->getMessage());
            }
        }

        if (class_exists(UserLog::class)) {
            UserLog::create([
                'user_id' => $user->id,
                'action' => 'create_user',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
                'description' => 'Akun pengguna baru dibuat oleh superadmin.',
            ]);
        }

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function editPengguna(User $user)
    {
        $departments = Department::orderBy('name')->get();
        return view('superadmin.pengguna.edit', compact('user', 'departments'));
    }

    public function updatePengguna(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'role' => 'required|string|max:50',
            'nip' => 'required|string|max:50',
            'unit_kerja' => 'required|string|max:150',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|in:Aktif,Nonaktif',
            'password' => 'nullable|string|min:6|confirmed',
            'face_image' => 'nullable|string', // Base64 string
        ]);

        $data = [
            'username' => $validated['username'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'nip' => $validated['nip'] ?? null,
            'unit_kerja' => $validated['unit_kerja'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'status' => $validated['status'] ?? $user->status,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        // Handing Face Image
        $nipFallback = $validated['nip'] ?? $user->nip ?? $user->id;
        
        Log::info('Checking face_image in updatePengguna', [
            'has_face' => $request->filled('face_image'),
            'nip' => $nipFallback ?? 'null',
            'user_id' => $user->id
        ]);

        if ($request->filled('face_image')) {
            try {
                $base64Image = $request->face_image;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                        throw new \Exception('Invalid image type: ' . $type);
                    }

                    $imageData = base64_decode($base64Image);
                    if ($imageData === false) {
                        throw new \Exception('base64_decode failed');
                    }
                } else {
                    throw new \Exception('did not match data URI with image data');
                }

                $fileName = 'faces/' . $nipFallback . '_' . time() . '.' . $type;
                Storage::disk('public')->put($fileName, $imageData);

                // Find or create Employee
                $employee = Employee::firstOrCreate(
                    ['nip' => $nipFallback],
                    ['name' => $validated['name'], 'status' => 'Aktif']
                );

                // Deactivate old faces
                EmployeeFace::where('employee_id', $employee->id)->update(['is_active' => false]);

                // Store Face
                EmployeeFace::create([
                    'employee_id' => $employee->id,
                    'image_path' => $fileName,
                    'is_active' => true,
                ]);

                // SYNC WITH FACE SERVICE (PYTHON)
                try {
                    $response = Http::attach(
                        'face_image',
                        $imageData,
                        basename($fileName)
                    )->post('http://127.0.0.1:5001/register', [
                        'user_id' => $user->id,
                    ]);

                    if ($response->successful()) {
                        Log::info('Face service sync successful for User ID: ' . $user->id);
                    } else {
                        Log::error('Face service sync failed: ' . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error('Face service connection error: ' . $e->getMessage());
                }

                Log::info('Face image updated successfully for NIP/ID: ' . $nipFallback);

            } catch (\Exception $e) {
                Log::error('Error saving face image in updatePengguna: ' . $e->getMessage());
            }
        }

        if (class_exists(UserLog::class)) {
            UserLog::create([
                'user_id' => $user->id,
                'action' => 'update_user',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
                'description' => 'Data pengguna diperbarui oleh superadmin.',
            ]);
        }

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function resetPassword(Request $request, User $user)
    {
        $newPassword = 'password123';

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        if (class_exists(UserLog::class)) {
            UserLog::create([
                'user_id' => $user->id,
                'action' => 'reset_password',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
                'description' => 'Password pengguna direset oleh superadmin.',
            ]);
        }

        return back()->with('success', 'Password direset ke password123');
    }

    public function toggleStatus(Request $request, User $user)
    {
        $newStatus = $user->status === 'Aktif' ? 'Nonaktif' : 'Aktif';

        $user->update([
            'status' => $newStatus,
        ]);

        if (class_exists(UserLog::class)) {
            UserLog::create([
                'user_id' => $user->id,
                'action' => 'toggle_status',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
                'description' => 'Status pengguna diubah menjadi ' . $newStatus . '.',
            ]);
        }

        return back()->with('success', 'Status pengguna diperbarui');
    }

    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        User::whereIn('id', $validated['user_ids'])->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('superadmin.pengguna.index')
            ->with('success', 'Status pengguna berhasil diperbarui secara massal');
    }

    public function destroyPengguna(User $user)
    {
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Superadmin tidak bisa dihapus');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus');
    }

    public function riwayatLogin(User $user)
    {
        $logs = class_exists(UserLog::class)
            ? UserLog::where('user_id', $user->id)->latest()->paginate(20)
            : collect();

        return view('superadmin.pengguna.riwayat-login', compact('user', 'logs'));
    }

    public function perangkat(User $user)
    {
        $devices = class_exists(UserDevice::class)
            ? UserDevice::where('user_id', $user->id)->latest()->get()
            : collect();

        return view('superadmin.pengguna.perangkat', compact('user', 'devices'));
    }

    public function lokasiAbsen(User $user)
    {
        return view('superadmin.pengguna.lokasi-absen', compact('user'));
    }

    public function detailAkses(User $user)
    {
        return view('superadmin.pengguna.detail-akses', compact('user'));
    }

    public function exportPengguna(): StreamedResponse
    {
        $fileName = 'data_pengguna.csv';
        $users = User::latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Username',
                'Nama Lengkap',
                'Email',
                'NIP',
                'Role',
                'Unit Kerja',
                'Status',
            ]);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->username,
                    $user->name,
                    $user->email,
                    $user->nip,
                    $user->role,
                    $user->unit_kerja,
                    $user->status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importPengguna(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->with('error', 'File tidak dapat dibaca.');
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File kosong atau format tidak valid.');
        }

        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);

            if (empty($data['username']) || empty($data['name']) || empty($data['email']) || empty($data['role'])) {
                $skipped++;
                continue;
            }

            $exists = User::where('username', $data['username'])
                ->orWhere('email', $data['email'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            User::create([
                'username' => trim($data['username']),
                'name' => trim($data['name']),
                'email' => trim($data['email']),
                'password' => Hash::make($data['password'] ?? 'password123'),
                'nip' => $data['nip'] ?? $data['nrp'] ?? null,
                'role' => trim($data['role']),
                'unit_kerja' => $data['unit_kerja'] ?? $data['opd'] ?? null,
                'status' => $data['status'] ?? 'Aktif',
            ]);

            $imported++;
        }

        fclose($handle);

        return back()->with('success', "Import selesai. Berhasil: {$imported}, Dilewati: {$skipped}");
    }
}