<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\DepartmentScope;

class FaultReport extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new DepartmentScope);
    }

    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'status',
        'handled_by',
        'report_date',
        'evidence_path',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}