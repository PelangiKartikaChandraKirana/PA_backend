<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeWorkLeave extends Model
{
    protected $table = 'employee_work_leaves';

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}