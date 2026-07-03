<?php

namespace Database\Seeders;

use App\Models\AttendanceWeeklyScheduleCategory;
use App\Models\AttendanceWeeklySchedule;
use Illuminate\Database\Seeder;

class LamonganScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori Jadwal Normal
        $normalCategory = AttendanceWeeklyScheduleCategory::firstOrCreate([
            'name' => 'Jam Kerja Normal ASN Lamongan'
        ], [
            'priority' => 1
        ]);

        // Kategori Jadwal Ramadhan
        $ramadhanCategory = AttendanceWeeklyScheduleCategory::firstOrCreate([
            'name' => 'Jam Kerja Ramadhan ASN Lamongan'
        ], [
            'priority' => 2
        ]);

        // Hari Senin - Kamis Normal
        for ($i = 1; $i <= 4; $i++) {
            AttendanceWeeklySchedule::updateOrCreate(
                ['category_id' => $normalCategory->id, 'day_of_week' => $i],
                [
                    'start_time' => '07:00:00', // Jam masuk dimulai
                    'end_time' => '15:30:00', // Jam pulang normal
                    'tolerance_minutes' => 30, // 07:00 - 07:30
                    'effective_minutes' => 450 // Asumsi 7.5 jam
                ]
            );

            // Ramadhan
            AttendanceWeeklySchedule::updateOrCreate(
                ['category_id' => $ramadhanCategory->id, 'day_of_week' => $i],
                [
                    'start_time' => '07:30:00',
                    'end_time' => '15:00:00',
                    'tolerance_minutes' => 30, // 07:30 - 08:00
                    'effective_minutes' => 420 // Asumsi 7 jam
                ]
            );
        }

        // Hari Jumat
        AttendanceWeeklySchedule::updateOrCreate(
            ['category_id' => $normalCategory->id, 'day_of_week' => 5],
            [
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'tolerance_minutes' => 30,
                'effective_minutes' => 420
            ]
        );

        AttendanceWeeklySchedule::updateOrCreate(
            ['category_id' => $ramadhanCategory->id, 'day_of_week' => 5],
            [
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'tolerance_minutes' => 30,
                'effective_minutes' => 420
            ]
        );
    }
}
