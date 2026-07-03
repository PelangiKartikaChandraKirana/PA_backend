<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexName = 'face_audit_ml_created_idx';

    public function up(): void
    {
        if (!Schema::hasTable('face_verification_audit_logs')) {
            return;
        }

        $existing = collect(DB::select('SHOW INDEX FROM face_verification_audit_logs'))
            ->pluck('Key_name')
            ->all();

        if (in_array($this->indexName, $existing, true)) {
            return;
        }

        Schema::table('face_verification_audit_logs', function (Blueprint $table) {
            $table->index(['matched', 'liveness_passed', 'created_at'], $this->indexName);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('face_verification_audit_logs')) {
            return;
        }

        $existing = collect(DB::select('SHOW INDEX FROM face_verification_audit_logs'))
            ->pluck('Key_name')
            ->all();

        if (!in_array($this->indexName, $existing, true)) {
            return;
        }

        Schema::table('face_verification_audit_logs', function (Blueprint $table) {
            $table->dropIndex($this->indexName);
        });
    }
};
