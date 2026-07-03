<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_weekly_schedules', function (Blueprint $table) {
            $table->id();

            // relasi ke kategori jadwal kerja
            $table->foreignId('category_id')
                ->constrained('attendance_weekly_schedule_categories')
                ->cascadeOnDelete();

            // 1=Senin ... 7=Minggu
            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');

            // toleransi keterlambatan (menit)
            $table->unsignedSmallInteger('tolerance_minutes')->default(0);

            // jam kerja efektif (menit) opsional
            $table->unsignedSmallInteger('effective_minutes')->nullable();

            // tipe pegawai (opsional)
            $table->string('employee_type')->nullable(); // contoh: asn/non-asn/shift

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // biar 1 kategori tidak dobel jadwal untuk hari yang sama
            $table->unique(['category_id', 'day_of_week']);
            $table->index(['category_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_weekly_schedules');
    }
};