<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineFault extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'machine_fault_type_id',
        'machine_fault_status_id',
        'incident_date',
        'description',
        'evidence_path',
    ];

    protected $casts = [
        'incident_date' => 'date',
    ];

    public function machineFaultType()
    {
        return $this->belongsTo(MachineFaultType::class, 'machine_fault_type_id');
    }

    public function machineFaultStatus()
    {
        return $this->belongsTo(MachineFaultStatus::class, 'machine_fault_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}