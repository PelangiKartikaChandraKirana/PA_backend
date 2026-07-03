<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\AttendanceLog;
use App\Models\AttendanceLogStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Setup a dummy status if doesn't exist
        $statusHadir = AttendanceLogStatus::firstOrCreate(['name' => 'Hadir Tepat Waktu'], ['code' => 'P-01']);
        $statusTelat = AttendanceLogStatus::firstOrCreate(['name' => 'Hadir Terlambat'], ['code' => 'T-01']);
        
        $monthsToSeed = [Carbon::now()->format('Y-m'), Carbon::now()->subMonth()->format('Y-m')];

        // Seed 5 Users
        for ($i = 1; $i <= 5; $i++) {
            $nip = '1990' . rand(100000, 999999) . rand(10, 99);
            
            $user = User::updateOrCreate(
                ['email' => "pegawai{$i}@gmail.com"],
                [
                    'name' => "Pegawai Dummy {$i}",
                    'username' => "pegawai{$i}",
                    'nip' => $nip,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'unit_kerja' => 'Dinas Pendidikan',
                    'status' => 'Aktif',
                    'is_active' => true,
                ]
            );

            $employee = Employee::firstOrCreate(
                ['nip' => $user->nip],
                [
                    'name' => $user->name,
                    'status' => 'Aktif',
                ]
            );

            // Generate some attendances for the current month
            for ($day = 1; $day <= Carbon::now()->day; $day++) {
                // Skip weekends
                $dateObj = Carbon::create(Carbon::now()->year, Carbon::now()->month, $day);
                if ($dateObj->isWeekend()) {
                    continue;
                }
                
                // Randomly assign some to be absent (about 10% chance)
                if (rand(1, 10) > 9) {
                    continue;
                }

                $isLate = rand(1, 10) > 8; // 20% chance late
                
                $checkInObj = (clone $dateObj)->setHour(7)->setMinute($isLate ? rand(30, 59) : rand(0, 20)); // if late 7:30-7:59, if early 7:00-7:20
                $checkOutObj = (clone $dateObj)->setHour(16)->setMinute(rand(0, 30));

                AttendanceLog::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'attendance_date' => $dateObj->toDateString(),
                    ],
                    [
                        'status_id' => $isLate ? $statusTelat->id : $statusHadir->id,
                        'check_in_at' => $checkInObj,
                        'check_out_at' => $checkOutObj,
                    ]
                );
            }
        }
    }
}
