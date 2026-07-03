<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklySchedule extends Model
{
    protected $table = 'attendance_weekly_schedules';

    protected $fillable = [
        'category_id',
        'day_of_week',
        'start_time',
        'end_time',
        'tolerance_minutes',
        'effective_minutes',
        'employee_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'tolerance_minutes' => 'integer',
        'effective_minutes' => 'integer',
    ];
}