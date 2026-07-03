<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\DepartmentScope;

class AbsenceDocument extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new DepartmentScope);
    }

    protected $fillable = [
        'employee_id',
        'document_type',
        'title',
        'file_path',
        'start_date',
        'end_date',
        'lokasi_tujuan',
        'nama_kegiatan',
        'status',
        'approved_by',
        'rejected_by',
        'decision_notes',
        'decided_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'decided_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}