<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nip' => ['required'],
            'password' => ['required'],
            'device_id' => ['required', 'string'], // Wajib dikirim oleh Mobile App
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'NIP atau password salah',
            ], 401);
        }

        // --- Logic: Device Binding ---
        // Cek apakah user punya perangkat aktif
        $activeDevice = $user->devices()->where('is_active', true)->first();

        if (!$activeDevice) {
            // Jika belum ada perangkat terdaftar, otomatis daftarkan perangkat ini
            $user->devices()->create([
                'device_id' => $request->device_id,
                'registered_at' => now(),
                'is_active' => true,
            ]);
        } else {
            // Jika sudah ada, pastikan ID perangkat yang dikirim sama
            if ($activeDevice->device_id !== $request->device_id) {
                return response()->json([
                    'message' => 'Akun Anda sudah tertaut dengan perangkat lain. Silakan hubungi admin untuk reset perangkat.',
                    'error_code' => 'DEVICE_MISMATCH'
                ], 403);
            }
        }
        // --- End Logic ---

        // Optional: cek status user
        if (isset($user->status) && strtolower($user->status) !== 'aktif') {
            return response()->json([
                'message' => 'Akun tidak aktif',
            ], 403);
        }

        $token = $user->createToken('flutter-token')->plainTextToken;

        $employee = \App\Models\Employee::where('nip', $user->nip)->first();

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'nip' => $user->nip,
                'role' => $user->role,
                'unit_kerja' => $user->unit_kerja,
                'status' => $user->status,
                'tpp_allowance' => $employee ? $employee->tpp_allowance : 0,
            ],
        ], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $employee = \App\Models\Employee::where('nip', $user->nip)->first();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'nip' => $user->nip,
                'role' => $user->role,
                'unit_kerja' => $user->unit_kerja,
                'status' => $user->status,
                'tpp_allowance' => $employee ? $employee->tpp_allowance : 0,
            ],
        ], 200);
    }
}