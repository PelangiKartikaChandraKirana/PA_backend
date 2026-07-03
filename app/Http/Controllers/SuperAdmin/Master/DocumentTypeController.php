<?php

namespace App\Http\Controllers\SuperAdmin\Master;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{

    public function index()
    {
        $items = DocumentType::orderBy('name')->paginate(10);

        return view('superadmin.master.tipe-dokumen.index',compact('items'));
    }

    public function create()
    {
        return view('superadmin.master.tipe-dokumen.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required',
            'code'=>'nullable',
            'description'=>'nullable',
            'category'=>'nullable',
            'color'=>'nullable',
            'requires_approval'=>'nullable',
            'is_required'=>'nullable',
            'is_active'=>'required'
        ]);

        DocumentType::create($data);

        return redirect()
            ->route('superadmin.master.tipe-dokumen.index')
            ->with('success','Tipe dokumen berhasil ditambahkan');
    }

    public function edit(DocumentType $tipe_dokumen)
    {
        return view(
            'superadmin.master.tipe-dokumen.edit',
            ['item' => $tipe_dokumen]
        );
    }

    public function update(Request $request, DocumentType $tipe_dokumen)
    {
        $data = $request->validate([
            'name'=>'required',
            'code'=>'nullable',
            'description'=>'nullable',
            'category'=>'nullable',
            'color'=>'nullable',
            'requires_approval'=>'nullable',
            'is_required'=>'nullable',
            'is_active'=>'required'
        ]);

        $tipe_dokumen->update($data);

        return redirect()
            ->route('superadmin.master.tipe-dokumen.index')
            ->with('success','Data berhasil diperbarui');
    }

    public function destroy(DocumentType $tipe_dokumen)
    {
        $tipe_dokumen->delete();

        return back()->with('success','Data berhasil dihapus');
    }

}