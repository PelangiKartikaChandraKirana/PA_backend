<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->time('check_in_at')->nullable();
            $table->time('check_out_at')->nullable();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('attendance_log_statuses')
                ->nullOnDelete();

            $table->string('check_in_photo_path')->nullable();
            $table->string('check_out_photo_path')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['attendance_date']);
            $table->index(['employee_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};