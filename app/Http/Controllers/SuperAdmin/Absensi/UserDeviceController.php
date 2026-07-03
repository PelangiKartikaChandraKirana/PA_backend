<?php

namespace App\Http\Controllers\SuperAdmin\Absensi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class UserDeviceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $status = $request->get('status');

        // Statistik Dashboard
        $stats = [
            'total' => UserDevice::count(),
            'active' => UserDevice::where('is_active', true)->count(),
            'inactive' => UserDevice::where('is_active', false)->count(),
            'users_no_device' => User::where('role', 'user')->whereDoesntHave('devices')->count(),
        ];

        // Query Utama
        $items = UserDevice::with('user')
            ->when($status !== null && $status !== '', function($q) use ($status) {
                return $q->where('is_active', $status == '1');
            })
            ->when($search, function($q) use ($search) {
                return $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.absensi.perangkat-pengguna.index', compact('items', 'stats', 'search', 'status'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('superadmin.absensi.perangkat-pengguna.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'device_id' => ['required','string','max:255'],
            'is_active' => ['nullable']
        ]);

        $data['is_active'] = $request->boolean('is_active');

        UserDevice::create($data);

        return redirect()
            ->route('superadmin.absensi.perangkat-pengguna.index')
            ->with('success','Perangkat berhasil ditambahkan');
    }

    public function edit(UserDevice $perangkat_pengguna)
    {
        $users = User::orderBy('name')->get();
        $item = $perangkat_pengguna;

        return view('superadmin.absensi.perangkat-pengguna.edit', compact('item','users'));
    }

    public function update(Request $request, UserDevice $perangkat_pengguna)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'device_id' => ['required','string','max:255'],
            'is_active' => ['nullable']
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $perangkat_pengguna->update($data);

        return redirect()
            ->route('superadmin.absensi.perangkat-pengguna.index')
            ->with('success','Perangkat berhasil diupdate');
    }

    public function destroy(UserDevice $perangkat_pengguna)
    {
        $perangkat_pengguna->delete();

        return back()->with('success','Perangkat berhasil dihapus');
    }
}