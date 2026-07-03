<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceVerificationAuditLog extends Model
{
    protected $fillable = [
        'attendance_log_id',
        'user_id',
        'employee_id',
        'type',
        'matched',
        'liveness_passed',
        'manual_review_required',
        'failed_attempts_today',
        'confidence',
        'distance',
        'threshold',
        'session_id',
        'device_id',
        'latitude',
        'longitude',
        'request_nonce',
        'request_ip',
        'failure_reason',
        'response_code',
        'service_message',
        'metadata',
    ];

    protected $casts = [
        'matched' => 'boolean',
        'liveness_passed' => 'boolean',
        'manual_review_required' => 'boolean',
        'failed_attempts_today' => 'integer',
        'confidence' => 'decimal:4',
        'distance' => 'decimal:4',
        'threshold' => 'decimal:4',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'metadata' => 'array',
    ];
}
