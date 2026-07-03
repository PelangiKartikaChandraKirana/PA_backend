<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_scan_logs', function (Blueprint $table) {
            $table->decimal('distance', 8, 4)->nullable()->after('confidence');
            $table->string('session_id', 128)->nullable()->after('distance');
            $table->boolean('liveness_passed')->default(false)->after('session_id');
            $table->string('device_id', 255)->nullable()->after('liveness_passed');
            $table->decimal('latitude', 10, 7)->nullable()->after('device_id');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('request_nonce', 255)->nullable()->after('longitude');
            $table->string('request_ip', 45)->nullable()->after('request_nonce');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_scan_logs', function (Blueprint $table) {
            $table->dropColumn([
                'distance',
                'session_id',
                'liveness_passed',
                'device_id',
                'latitude',
                'longitude',
                'request_nonce',
                'request_ip',
            ]);
        });
    }
};

