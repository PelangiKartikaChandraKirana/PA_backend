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
        Schema::table('absence_documents', function (Blueprint $table) {
            $table->string('lokasi_tujuan')->nullable()->after('end_date');
            $table->string('nama_kegiatan')->nullable()->after('lokasi_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absence_documents', function (Blueprint $table) {
            $table->dropColumn(['lokasi_tujuan', 'nama_kegiatan']);
        });
    }
};
