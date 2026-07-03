<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'radius')) {
                $table->integer('radius')->nullable();
            }

            if (!Schema::hasColumn('locations', 'unit')) {
                $table->string('unit')->nullable();
            }

            if (!Schema::hasColumn('locations', 'status')) {
                $table->boolean('status')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'radius')) {
                $table->dropColumn('radius');
            }

            if (Schema::hasColumn('locations', 'unit')) {
                $table->dropColumn('unit');
            }

            if (Schema::hasColumn('locations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};