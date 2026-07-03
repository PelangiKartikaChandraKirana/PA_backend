<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_weekly_schedule_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('priority')->default(1);
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_weekly_schedule_categories');
    }
};