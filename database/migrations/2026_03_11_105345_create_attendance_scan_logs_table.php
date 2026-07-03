<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_scan_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_log_id')
                ->constrained('attendance_logs')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', ['masuk', 'pulang']);

            $table->boolean('matched')->default(false);
            $table->decimal('confidence', 8, 4)->nullable();

            $table->string('face_image_path')->nullable();

            $table->timestamp('attendance_time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_scan_logs');
    }
};