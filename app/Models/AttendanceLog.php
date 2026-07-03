<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\DepartmentScope;

class AttendanceLog extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new DepartmentScope);
    }

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_at',
        'apel_at',
        'check_out_at',
        'status_id',
        'check_in_photo_path',
        'apel_photo_path',
        'check_out_photo_path',
        'note',
        'validation_status',
        'validation_reason',
        'time_difference_seconds',

    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function status()
    {
        return $this->belongsTo(AttendanceLogStatus::class, 'status_id');
    }

    public function scanLogs()
    {
        return $this->hasMany(AttendanceScanLog::class, 'attendance_log_id');
    }
}