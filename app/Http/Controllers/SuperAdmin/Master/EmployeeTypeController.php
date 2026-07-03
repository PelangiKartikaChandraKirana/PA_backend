<?php

namespace App\Http\Controllers\SuperAdmin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeType;

class EmployeeTypeController extends Controller
{

    public function index()
    {
        $types = EmployeeType::latest()->get();
        return view('superadmin.tipe-pegawai.index', compact('types'));
    }


    public function create()
    {
        return view('superadmin.tipe-pegawai.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        EmployeeType::create($request->all());

        return redirect()->route('superadmin.tipe-pegawai.index')
            ->with('success','Tipe Pegawai berhasil ditambahkan');
    }


    public function edit(EmployeeType $tipe_pegawai)
    {
        return view('superadmin.tipe-pegawai.edit', compact('tipe_pegawai'));
    }


    public function update(Request $request, EmployeeType $tipe_pegawai)
    {
        $tipe_pegawai->update($request->all());

        return redirect()->route('superadmin.tipe-pegawai.index')
            ->with('success','Tipe Pegawai berhasil diupdate');
    }


    public function destroy(EmployeeType $tipe_pegawai)
    {
        $tipe_pegawai->delete();

        return redirect()->route('superadmin.tipe-pegawai.index')
            ->with('success','Tipe Pegawai berhasil dihapus');
    }

}