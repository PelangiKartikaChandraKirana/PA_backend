<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->nullable(); // kalau kamu pakai departments
            $table->string('name');
            $table->string('serial_number')->nullable()->unique();
            $table->string('ip_address')->nullable();
            $table->string('location_name')->nullable();

            $table->timestamp('last_seen_at')->nullable(); // ini kunci status online/offline
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};