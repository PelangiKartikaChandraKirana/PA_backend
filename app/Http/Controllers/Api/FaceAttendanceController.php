<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AttendanceLogStatus;
use App\Models\AttendanceScanLog;
use App\Models\Employee;
use App\Models\FaceVerificationAuditLog;
use App\Models\AttendanceWeeklySchedule;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class FaceAttendanceController extends Controller
{
    private function allowServerCamera(): bool
    {
        return (bool) config('face.allow_server_camera', false);
    }

    private function allowDebugBypass(): bool
    {
        return (bool) config('face.allow_debug_bypass', false);
    }

    private function faceFailureStats(int $userId): array
    {
        $key = sprintf('face_failures:%d:%s', $userId, now()->toDateString());
        $count = Cache::increment($key);
        if ($count === 1) {
            Cache::put($key, 1, now()->endOfDay());
        }

        return [
            'count' => (int) $count,
            'manual_review_required' => $count >= 3,
        ];
    }

    private function resetFaceFailureStats(int $userId): void
    {
        $key = sprintf('face_failures:%d:%s', $userId, now()->toDateString());
        Cache::forget($key);
    }

    private function faceServiceUrl(): string
    {
        return config('services.face_service.url', 'http://127.0.0.1:5001');
    }

    private function faceHttpClient()
    {
        $timeout = (int) config('face.service_timeout_seconds', 60);
        $connectTimeout = (int) config('face.service_connect_timeout_seconds', 5);

        return Http::timeout($timeout)->connectTimeout($connectTimeout);
    }

    private function responsePayload(HttpResponse $response): array
    {
        $payload = $response->json();
        return is_array($payload) ? $payload : [
            'message' => trim((string) $response->body()) !== ''
                ? trim((string) $response->body())
                : 'Response tidak valid dari face service.',
        ];
    }

    private function logVerificationAudit(
        Request $request,
        array $result,
        int $statusCode,
        bool $matched,
        bool $livenessPassed,
        bool $manualReviewRequired = false,
        int $failedAttemptsToday = 0,
        ?int $attendanceLogId = null,
        ?int $employeeId = null
    ): void {
        try {
            FaceVerificationAuditLog::create([
                'attendance_log_id' => $attendanceLogId,
                'user_id' => optional($request->user())->id,
                'employee_id' => $employeeId,
                'type' => $request->input('type'),
                'matched' => $matched,
                'liveness_passed' => $livenessPassed,
                'manual_review_required' => $manualReviewRequired,
                'failed_attempts_today' => $failedAttemptsToday,
                'confidence' => $result['confidence'] ?? null,
                'distance' => $result['distance'] ?? null,
                'threshold' => $result['threshold'] ?? null,
                'session_id' => (string) $request->input('session_id', ''),
                'device_id' => (string) $request->header('X-Device-Id', ''),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'request_nonce' => (string) $request->header('X-Request-Nonce', ''),
                'request_ip' => (string) $request->ip(),
                'failure_reason' => $result['message'] ?? null,
                'response_code' => $statusCode,
                'service_message' => is_string($result['message'] ?? null) ? $result['message'] : null,
                'metadata' => [
                    'proof_ttl_seconds' => $result['proof_ttl_seconds'] ?? null,
                    'challenge_total_steps' => $result['challenge_total_steps'] ?? null,
                    'manual_review_required' => $manualReviewRequired,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan audit verifikasi wajah', [
                'error' => $e->getMessage(),
                'user_id' => optional($request->user())->id,
            ]);
        }
    }

    private function calculateDistanceInMeters($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function registerFace(Request $request)
    {
        $request->validate([
            'face_image' => 'required|image',
        ]);

        $user = $request->user();

        if (!$user->nip) {
            return response()->json([
                'message' => 'User ini belum memiliki NIP',
            ], 422);
        }

        $employee = Employee::where('nip', $user->nip)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Data pegawai dengan NIP tersebut tidak ditemukan',
            ], 404);
        }

        $response = $this->faceHttpClient()->attach(
            'face_image',
            file_get_contents($request->file('face_image')->getRealPath()),
            $request->file('face_image')->getClientOriginalName()
        )->post($this->faceServiceUrl() . '/register', [
            'user_id' => $user->id,
        ]);

        return response()->json($this->responsePayload($response), $response->status());
    }

    public function livenessStatus(Request $request)
    {
        $response = $this->faceHttpClient()->get($this->faceServiceUrl() . '/status');
        return response()->json($this->responsePayload($response), $response->status());
    }

    public function livenessFrame(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'face_image' => 'required|image',
            ]);

            $response = $this->faceHttpClient()->attach(
                'face_image',
                file_get_contents($request->file('face_image')->getRealPath()),
                $request->file('face_image')->getClientOriginalName()
            )->post($this->faceServiceUrl() . '/frame-upload');

            return response()->json($this->responsePayload($response), $response->status());
        }

        if (!$this->allowServerCamera()) {
            return response()->json([
                'message' => 'Streaming kamera server dinonaktifkan di environment ini.',
            ], 403);
        }

        $response = $this->faceHttpClient()->get($this->faceServiceUrl() . '/frame');
        return response()->json($this->responsePayload($response), $response->status());
    }

    public function livenessReset(Request $request)
    {
        $response = $this->faceHttpClient()->post($this->faceServiceUrl() . '/reset');
        return response()->json($this->responsePayload($response), $response->status());
    }

    public function verify(Request $request)
    {
        try {
            $isLocalLiveness = $request->boolean('local_liveness');

            $rules = [
                'face_image' => 'required|image',
                'type' => 'required|in:masuk,pulang,apel',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'device_time' => 'required|date',
            ];

            if (!$isLocalLiveness) {
                $rules['session_id'] = 'required|string';
                $rules['liveness_token'] = 'required|string';
            }

            $request->validate($rules);

            $debugMode = $request->boolean('debug_mode');
            if ($debugMode && !$this->allowDebugBypass()) {
                return response()->json([
                    'message' => 'debug_mode tidak diizinkan di environment ini.',
                ], 403);
            }

            $user = $request->user();

            if (!$user->nip) {
                return response()->json([
                    'message' => 'User ini belum memiliki NIP',
                ], 422);
            }

            $employee = Employee::where('nip', $user->nip)->with(['locations' => function($q) {
                $q->where('employee_locations.is_active', true);
            }])->first();

            if (!$employee) {
                return response()->json([
                    'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                ], 404);
            }

                        /*
            |--------------------------------------------------------------------------
            | Sinkronisasi Waktu Server
            |--------------------------------------------------------------------------
            */

            $deviceTime = Carbon::parse($request->device_time);

            $serverTime = now();

            $timeDifference = abs(
                $serverTime->diffInSeconds($deviceTime)
            );

            if ($timeDifference > 3) {

                AttendanceLog::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => today(),

                    'validation_status' => 'REJECTED',
                    'validation_reason' => 'TIME_NOT_SYNC',
                    'time_difference_seconds' => $timeDifference,

                    'note' => 'Waktu perangkat tidak sinkron',
                ]);

                return response()->json([
                    'message' => 'Waktu perangkat tidak sinkron.',
                    'difference_seconds' => $timeDifference,
                ], 422);
            }

            // --- Lokasi Geolocation Check ---
	    $minDistance = null;
            $assignedLocations = $employee->locations;
            if ($assignedLocations->isNotEmpty()) {
                $isWithinRadius = false;
                $userLat = (float) $request->latitude;
                $userLon = (float) $request->longitude;
                $minDistance = PHP_FLOAT_MAX;

                foreach ($assignedLocations as $location) {
                    $distance = $this->calculateDistanceInMeters(
                        $userLat, $userLon,
                        (float)$location->latitude, (float)$location->longitude
                    );

                    $minDistance = min($minDistance, $distance);
                    $radius = $location->radius_meters > 0 ? $location->radius_meters : 50;

                    if ($distance <= $radius) {
                        $isWithinRadius = true;
                        break;
                    }
                }

                if (!$isWithinRadius) {
                    AttendanceLog::create([
                        'employee_id' => $employee->id,
                        'attendance_date' => today(),

                        'validation_status' => 'REJECTED',
                        'validation_reason' => 'OUTSIDE_GEOFENCE',

                        'note' => 'Presensi ditolak karena berada di luar radius lokasi',
                    ]);

                    return response()->json([
                        'message' => 'Anda berada di luar area presensi.',
                        'distance' => round($minDistance),
                    ], 422);
                }
            }
            // --- End Lokasi Check ---

            $endpoint = $isLocalLiveness ? '/verify_face_only' : '/verify';
            $postData = ['user_id' => $user->id];
            
            if (!$isLocalLiveness) {
                $postData['session_id'] = $request->session_id;
                $postData['liveness_token'] = $request->liveness_token;
            }

            $response = $this->faceHttpClient()->attach(
                'face_image',
                file_get_contents($request->file('face_image')->getRealPath()),
                $request->file('face_image')->getClientOriginalName()
            )->post($this->faceServiceUrl() . $endpoint, $postData);

            $result = $this->responsePayload($response);

            if (!$response->successful()) {
                $stats = $this->faceFailureStats((int) $user->id);
                $payload = array_merge($result, [
                    'manual_review_required' => $stats['manual_review_required'],
                    'failed_attempts_today' => $stats['count'],
                ]);
                $this->logVerificationAudit(
                    $request,
                    $payload,
                    $response->status(),
                    false,
                    false,
                    $stats['manual_review_required'],
                    $stats['count'],
                    null,
                    (int) $employee->id
                );

                return response()->json($payload, $response->status());
            }

            if (!($result['matched'] ?? false)) {
                $stats = $this->faceFailureStats((int) $user->id);
                $payload = [
                    'message' => 'Wajah tidak dikenali',
                    'matched' => false,
                    'confidence' => $result['confidence'] ?? null,
                    'distance' => $result['distance'] ?? null,
                    'threshold' => $result['threshold'] ?? null,
                    'manual_review_required' => $stats['manual_review_required'],
                    'failed_attempts_today' => $stats['count'],
                ];
                $this->logVerificationAudit(
                    $request,
                    $payload,
                    422,
                    false,
                    true,
                    $stats['manual_review_required'],
                    $stats['count'],
                    null,
                    (int) $employee->id
                );

                return response()->json($payload, 422);
            }

            $this->resetFaceFailureStats((int) $user->id);

            $today = now()->toDateString();

            $attendanceTime = now();


            // Automatically create the status if it is missing in the database
           $todaySchedule = AttendanceWeeklySchedule::where('day_of_week', now()->dayOfWeek)
                ->where('employee_type', $employee->employee_type_id)
                ->where('is_active', true)
                ->first();

            $statusCode = 'ONTIME';
            $statusName = 'Hadir Tepat Waktu';

            if (
                $request->type === 'masuk' &&
                $todaySchedule
            ) {

                $scheduleStart = Carbon::parse(
                    today()->format('Y-m-d') . ' ' . $todaySchedule->start_time
                );

                $lateLimit = $scheduleStart->copy()
                    ->addMinutes($todaySchedule->tolerance_minutes);

                if ($attendanceTime->gt($lateLimit)) {

                    $statusCode = 'LATE';
                    $statusName = 'Terlambat';

                }

            }

            $status = AttendanceLogStatus::firstOrCreate(
                ['code' => $statusCode],
                ['name' => $statusName]
            );

            $attendanceLog = AttendanceLog::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'attendance_date' => $today,
                ],
                [
                    'status_id' => $status->id,
                    'note' => 'Absensi via face recognition',
                    'validation_status' => 'VALID',

                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'distance_meter' => $minDistance !== null ? round($minDistance) : null,
                ]
            );

            $attendanceLog->status_id = $status->id;
            $attendanceLog->save();

            $photoPath = null;
            $message = 'Absensi berhasil';
            $hadCheckInBefore = !empty($attendanceLog->check_in_at);
            $hadCheckOutBefore = !empty($attendanceLog->check_out_at);

            if ($request->type === 'masuk') {
                if ($hadCheckInBefore && !$debugMode) {
                    $payload = [
                        'message' => 'Anda sudah melakukan absensi masuk hari ini',
                        'matched' => true,
                    ];
                    $this->logVerificationAudit(
                        $request,
                        $payload,
                        422,
                        true,
                        true,
                        false,
                        0,
                        (int) $attendanceLog->id,
                        (int) $employee->id
                    );

                    return response()->json($payload, 422);
                }

                $attendanceLog->check_in_at = $attendanceTime->format('H:i:s');
                $photoPath = $request->file('face_image')
                    ->store('attendance_faces/checkin', 'public');
                $attendanceLog->check_in_photo_path = $photoPath;
            }

            if ($request->type === 'pulang') {
                if (!$attendanceLog->check_in_at && !$debugMode) {
                    $payload = [
                        'message' => 'Anda belum melakukan absensi masuk hari ini',
                        'matched' => true,
                    ];
                    $this->logVerificationAudit(
                        $request,
                        $payload,
                        422,
                        true,
                        true,
                        false,
                        0,
                        (int) $attendanceLog->id,
                        (int) $employee->id
                    );

                    return response()->json($payload, 422);
                }

                if ($hadCheckOutBefore && !$debugMode) {
                    $payload = [
                        'message' => 'Anda sudah melakukan absensi pulang hari ini',
                        'matched' => true,
                    ];
                    $this->logVerificationAudit(
                        $request,
                        $payload,
                        422,
                        true,
                        true,
                        false,
                        0,
                        (int) $attendanceLog->id,
                        (int) $employee->id
                    );

                    return response()->json($payload, 422);
                }

                $attendanceLog->check_out_at = $attendanceTime->format('H:i:s');
                $photoPath = $request->file('face_image')
                    ->store('attendance_faces/checkout', 'public');
                $attendanceLog->check_out_photo_path = $photoPath;
            }

            if ($request->type === 'apel') {
                if (!empty($attendanceLog->apel_at) && !$debugMode) {
                    $payload = [
                        'message' => 'Anda sudah melakukan absensi apel hari ini',
                        'matched' => true,
                    ];
                    $this->logVerificationAudit(
                        $request, $payload, 422, true, true, false, 0, (int) $attendanceLog->id, (int) $employee->id
                    );
                    return response()->json($payload, 422);
                }

                $attendanceLog->apel_at = $attendanceTime->format('H:i:s');
                $photoPath = $request->file('face_image')
                    ->store('attendance_faces/apel', 'public');
                $attendanceLog->apel_photo_path = $photoPath;
            }

            $attendanceLog->save();

            AttendanceScanLog::create([
                'attendance_log_id' => $attendanceLog->id,
                'user_id' => $user->id,
                'type' => $request->type,
                'matched' => true,
                'confidence' => $result['confidence'] ?? null,
                'distance' => $result['distance'] ?? null,
                'session_id' => $request->session_id,
                'liveness_passed' => true,
                'device_id' => (string) $request->header('X-Device-Id', ''),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'request_nonce' => (string) $request->header('X-Request-Nonce', ''),
                'request_ip' => (string) $request->ip(),
                'face_image_path' => $photoPath,
                'attendance_time' => $attendanceTime,
            ]);

            $payload = [
                'message' => $message,
                'matched' => true,
                'confidence' => $result['confidence'] ?? null,
                'distance' => $result['distance'] ?? null,
                'threshold' => $result['threshold'] ?? null,
                'type' => $request->type,
                'attendance_date' => optional($attendanceLog->attendance_date)->format('Y-m-d'),
                'check_in_at' => $attendanceLog->check_in_at,
                'check_out_at' => $attendanceLog->check_out_at,
                'attendance_time' => $attendanceTime->format('Y-m-d H:i:s'),
            ];

            $this->logVerificationAudit(
                $request,
                $payload,
                200,
                true,
                true,
                false,
                0,
                (int) $attendanceLog->id,
                (int) $employee->id
            );

            return response()->json($payload, 200);
        } catch (ValidationException $e) {
            $payload = [
                'message' => 'Validasi request gagal.',
                'errors' => $e->errors(),
            ];
            $this->logVerificationAudit($request, $payload, 422, false, false);
            return response()->json($payload, 422);
        } catch (\Throwable $e) {
            Log::error('Face verify endpoint error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $payload = app()->isLocal()
                ? [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]
                : [
                    'message' => 'Terjadi kesalahan internal saat verifikasi wajah.',
                ];
            $this->logVerificationAudit($request, $payload, 500, false, false);
            return response()->json($payload, 500);
        }
    }

    public function history(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->nip) {
                return response()->json([
                    'message' => 'User ini belum memiliki NIP',
                    'data' => [],
                ], 422);
            }

            $employee = Employee::where('nip', $user->nip)->first();

            if (!$employee) {
                return response()->json([
                    'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                    'data' => [],
                ], 404);
            }

            $logs = AttendanceLog::with(['status', 'scanLogs' => function ($query) {
                    $query->orderByDesc('attendance_time');
                }])
                ->where('employee_id', $employee->id)
                ->orderByDesc('attendance_date')
                ->orderByDesc('check_in_at')
                ->get();

            return response()->json([
                'message' => 'Riwayat absensi berhasil diambil',
                'data' => $logs->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'employee_id' => $item->employee_id,
                        'attendance_date' => optional($item->attendance_date)->format('Y-m-d'),
                        'check_in_at' => $item->check_in_at,
                        'apel_at' => $item->apel_at,
                        'check_out_at' => $item->check_out_at,
                        'status_id' => $item->status_id,
                        'status' => $item->status ? [
                            'id' => $item->status->id,
                            'code' => $item->status->code,
                            'name' => $item->status->name,
                        ] : null,
                        'check_in_photo_path' => $item->check_in_photo_path,
                        'apel_photo_path' => $item->apel_photo_path,
                        'check_out_photo_path' => $item->check_out_photo_path,
                        'note' => $item->note,
                        'scan_logs' => $item->scanLogs->map(function ($scan) {
                            return [
                                'id' => $scan->id,
                                'type' => $scan->type,
                                'matched' => $scan->matched,
                                'confidence' => $scan->confidence,
                                'distance' => $scan->distance,
                                'session_id' => $scan->session_id,
                                'liveness_passed' => $scan->liveness_passed,
                                'device_id' => $scan->device_id,
                                'latitude' => $scan->latitude,
                                'longitude' => $scan->longitude,
                                'request_nonce' => $scan->request_nonce,
                                'request_ip' => $scan->request_ip,
                                'attendance_time' => optional($scan->attendance_time)->format('Y-m-d H:i:s'),
                                'face_image_path' => $scan->face_image_path,
                            ];
                        })->values(),
                    ];
                }),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function todayAttendance(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->nip) {
                return response()->json([
                    'message' => 'User ini belum memiliki NIP',
                    'data' => null,
                ], 422);
            }

            $employee = Employee::where('nip', $user->nip)->first();

            if (!$employee) {
                return response()->json([
                    'message' => 'Data pegawai tidak ditemukan berdasarkan NIP user',
                    'data' => null,
                ], 404);
            }

            $today = now()->toDateString();

            $log = AttendanceLog::with('status')
                ->where('employee_id', $employee->id)
                ->whereDate('attendance_date', $today)
                ->first();

            return response()->json([
                'message' => 'Status absensi hari ini berhasil diambil',
                'data' => $log ? [
                    'id' => $log->id,
                    'employee_id' => $log->employee_id,
                    'attendance_date' => optional($log->attendance_date)->format('Y-m-d'),
                    'check_in_at' => $log->check_in_at,
                    'apel_at' => $log->apel_at,
                    'check_out_at' => $log->check_out_at,
                    'status_id' => $log->status_id,
                    'status' => $log->status ? [
                        'id' => $log->status->id,
                        'code' => $log->status->code,
                        'name' => $log->status->name,
                    ] : null,
                    'check_in_photo_path' => $log->check_in_photo_path,
                    'apel_photo_path' => $log->apel_photo_path,
                    'check_out_photo_path' => $log->check_out_photo_path,
                    'note' => $log->note,
                ] : null,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}
