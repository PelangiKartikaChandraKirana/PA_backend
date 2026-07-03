<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineFaultMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('machine_fault_statuses')->insertOrIgnore([
            ['name' => 'Proses', 'key' => 'proses', 'priority' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Selesai', 'key' => 'selesai', 'priority' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('machine_fault_types')->insertOrIgnore([
            ['name' => 'Mesin rusak / error', 'priority' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lokasi/GPS bermasalah', 'priority' => 3, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aplikasi bermasalah', 'priority' => 4, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}