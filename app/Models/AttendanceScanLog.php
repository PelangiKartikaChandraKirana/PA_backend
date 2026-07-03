<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceScanLog extends Model
{
    protected $fillable = [
        'attendance_log_id',
        'user_id',
        'type',
        'matched',
        'confidence',
        'distance',
        'session_id',
        'liveness_passed',
        'device_id',
        'latitude',
        'longitude',
        'request_nonce',
        'request_ip',
        'face_image_path',
        'attendance_time',
    ];

    protected $casts = [
        'matched' => 'boolean',
        'liveness_passed' => 'boolean',
        'confidence' => 'decimal:4',
        'distance' => 'decimal:4',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'attendance_time' => 'datetime',
    ];

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class, 'attendance_log_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
