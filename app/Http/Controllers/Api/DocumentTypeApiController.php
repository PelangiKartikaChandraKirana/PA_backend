<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = DocumentType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Daftar tipe dokumen berhasil diambil',
            'data' => $types->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'category' => $item->category,
                    'color' => $item->color ?? '#3b82f6',
                    'requires_approval' => (bool) $item->requires_approval,
                    'is_required' => (bool) $item->is_required,
                ];
            }),
        ]);
    }
}
