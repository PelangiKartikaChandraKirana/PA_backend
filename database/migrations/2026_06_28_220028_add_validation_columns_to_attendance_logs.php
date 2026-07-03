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
        Schema::table('attendance_logs', function (Blueprint $table) {
             $table->string('validation_status')
            ->default('VALID')
            ->after('note');

        $table->string('validation_reason')
            ->nullable()
            ->after('validation_status');

        $table->integer('time_difference_seconds')
            ->nullable()
            ->after('validation_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn([
            'validation_status',
            'validation_reason',
            'time_difference_seconds'
        ]);
        });
    }
};
