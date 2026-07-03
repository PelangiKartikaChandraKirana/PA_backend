<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = Company::latest()->paginate(10);

        return view('superadmin.company.index', compact('companies'));
    }

    public function create()
    {
        return view('superadmin.company.create');
    }

    public function store(Request $request)
    {
        Company::create($request->all());

        return redirect()->route('superadmin.master.instansi.index')
            ->with('success','Data berhasil ditambahkan');
    }

    public function edit(Company $instansi)
    {
        $company = $instansi;
        return view('superadmin.company.edit', compact('company'));
    }

    public function update(Request $request, Company $instansi)
    {
        $instansi->update($request->all());

        return redirect()->route('superadmin.master.instansi.index')
            ->with('success','Data berhasil diupdate');
    }

    public function destroy(Company $instansi)
    {
        $instansi->delete();

        return redirect()->route('superadmin.master.instansi.index')->with('success','Data berhasil dihapus');
    }

}