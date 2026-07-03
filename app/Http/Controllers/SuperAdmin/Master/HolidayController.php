<?php

namespace App\Http\Controllers\SuperAdmin\Master;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('superadmin.hari-libur.index', compact('holidays'));
    }

    public function create()
    {
        return view('superadmin.hari-libur.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'date' => 'required|date',
            'type' => 'required|string|max:50',
            'year' => 'nullable|integer',
            'description' => 'nullable|string',
            'company_id' => 'nullable|integer',
        ]);

        $validated['is_nasional'] = $request->has('is_nasional');
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['year'] = $validated['year'] ?? date('Y', strtotime($validated['date']));
        $validated['company_id'] = $validated['company_id'] ?? 0;

        Holiday::create($validated);

        return redirect()
            ->route('superadmin.hari-libur.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Holiday $hari_libur)
    {
        return view('superadmin.hari-libur.edit', [
            'holiday' => $hari_libur
        ]);
    }

    public function update(Request $request, Holiday $hari_libur)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'date' => 'required|date',
            'type' => 'required|string|max:50',
            'year' => 'nullable|integer',
            'description' => 'nullable|string',
            'company_id' => 'nullable|integer',
        ]);

        $validated['is_nasional'] = $request->has('is_nasional');
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['year'] = $validated['year'] ?? date('Y', strtotime($validated['date']));
        $validated['company_id'] = $validated['company_id'] ?? 0;

        $hari_libur->update($validated);

        return redirect()
            ->route('superadmin.hari-libur.index')
            ->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $hari_libur)
    {
        $hari_libur->delete();

        return redirect()
            ->route('superadmin.hari-libur.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}