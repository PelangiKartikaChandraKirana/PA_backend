<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FaceAttendanceController;
use App\Http\Controllers\Api\AttendanceScheduleController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AbsenceDocumentController;
use App\Http\Controllers\Api\FaultReportController;
use App\Http\Controllers\Api\DailyActivityController;
use App\Http\Controllers\Api\DocumentTypeApiController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/server-time', function () {
    return response()->json([
        'server_time' => now()->toIso8601String(),
        'timestamp' => now()->timestamp,
    ]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/tpp-percentage', [DashboardController::class, 'tppPercentage']);

    Route::get('/face/liveness/status', [FaceAttendanceController::class, 'livenessStatus'])
        ->middleware('throttle:face-status');

    Route::middleware(['throttle:face-action', 'request.nonce'])->group(function () {
        Route::post('/register-face', [FaceAttendanceController::class, 'registerFace']);
        Route::get('/face/liveness/frame', [FaceAttendanceController::class, 'livenessFrame']);
        Route::post('/face/liveness/reset', [FaceAttendanceController::class, 'livenessReset']);
        Route::post('/attendance-face', [FaceAttendanceController::class, 'verify']);
    });
    Route::post('/face/liveness/frame', [FaceAttendanceController::class, 'livenessFrame'])
        ->middleware(['throttle:face-frame', 'request.nonce']);
    Route::get('/attendance-history', [FaceAttendanceController::class, 'history']);
    Route::get('/today-attendance', [FaceAttendanceController::class, 'todayAttendance']);

    Route::get('/attendance-schedules', [AttendanceScheduleController::class, 'index']);
    Route::get('/attendance-schedules/{attendanceSchedule}', [AttendanceScheduleController::class, 'show']);
    Route::post('/attendance-schedules', [AttendanceScheduleController::class, 'store']);
    Route::put('/attendance-schedules/{attendanceSchedule}', [AttendanceScheduleController::class, 'update']);
    Route::delete('/attendance-schedules/{attendanceSchedule}', [AttendanceScheduleController::class, 'destroy']);

    Route::get('/laporan/presensi-harian', [LaporanController::class, 'presensiHarian']);
    Route::get('/laporan/presensi-bulanan', [LaporanController::class, 'presensiBulanan']);

    Route::get('/document-types', [DocumentTypeApiController::class, 'index']);
    Route::get('/absence-documents', [AbsenceDocumentController::class, 'index']);
    Route::post('/absence-documents', [AbsenceDocumentController::class, 'store']);
    Route::get('/absence-documents/{id}', [AbsenceDocumentController::class, 'show']);

    Route::get('/daily-activities', [DailyActivityController::class, 'index']);
    Route::post('/daily-activities', [DailyActivityController::class, 'store']);

    Route::get('/fault-reports', [FaultReportController::class, 'index']);
    Route::post('/fault-reports', [FaultReportController::class, 'store']);
    Route::get('/fault-reports/{id}', [FaultReportController::class, 'show']);
});
