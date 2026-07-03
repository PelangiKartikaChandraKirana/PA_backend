<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('face_verification_audit_logs')) {
            return;
        }

        Schema::create('face_verification_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_log_id')->nullable()->constrained('attendance_logs')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->enum('type', ['masuk', 'pulang'])->nullable();
            $table->boolean('matched')->default(false);
            $table->boolean('liveness_passed')->default(false);
            $table->boolean('manual_review_required')->default(false);
            $table->unsignedInteger('failed_attempts_today')->default(0);

            $table->decimal('confidence', 8, 4)->nullable();
            $table->decimal('distance', 8, 4)->nullable();
            $table->decimal('threshold', 8, 4)->nullable();

            $table->string('session_id', 128)->nullable();
            $table->string('device_id', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('request_nonce', 255)->nullable();
            $table->string('request_ip', 45)->nullable();

            $table->string('failure_reason', 255)->nullable();
            $table->unsignedSmallInteger('response_code')->default(200);
            $table->text('service_message')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'face_audit_user_created_idx');
            $table->index(['employee_id', 'created_at'], 'face_audit_emp_created_idx');
            $table->index(['matched', 'liveness_passed', 'created_at'], 'face_audit_ml_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_verification_audit_logs');
    }
};
