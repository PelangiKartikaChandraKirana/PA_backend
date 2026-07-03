<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceWeeklyScheduleCategory extends Model
{
    protected $table = 'attendance_weekly_schedule_categories';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'priority' => 'integer',
    ];

    public function schedules()
    {
        return $this->hasMany(AttendanceWeeklySchedule::class, 'category_id');
    }
}