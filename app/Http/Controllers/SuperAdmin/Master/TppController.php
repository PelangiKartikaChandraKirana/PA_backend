<?php

namespace App\Http\Controllers\SuperAdmin\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class TppController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        
        $employees = Employee::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('nip', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.master.tpp.index', compact('employees', 'q'));
    }

    public function update(Request $request, Employee $tpp)
    {
        $request->validate([
            'tpp_allowance' => 'required|numeric|min:0'
        ]);

        $tpp->update([
            'tpp_allowance' => $request->tpp_allowance
        ]);

        return back()->with('success', 'Besaran TPP berhasil diperbarui.');
    }
}
