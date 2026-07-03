<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\MachineFault;
use App\Models\Holiday;
use App\Models\AbsenceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)->toDateString()
            : now()->toDateString();

        $totalPegawai = class_exists(Employee::class) ? Employee::count() : 0;
        $totalUser = class_exists(User::class) ? User::count() : 0;
        $totalUserAktif = class_exists(User::class)
            ? User::where('status', 'Aktif')->count()
            : 0;

        $hadirHariIni = 0;
        $terlambatHariIni = 0;
        $belumPresensi = $totalPegawai;
        $ditolakGeofence = 0;
        $ditolakWaktu = 0;
        $validHariIni = 0;
        $rejectedHariIni = 0;

        $pendingKendala = class_exists(MachineFault::class)
            ? MachineFault::where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'Selesai');
            })->count()
            : 0;

        $hariLiburTerdekat = class_exists(Holiday::class)
            ? Holiday::whereDate('date', '>=', $tanggal)->orderBy('date')->first()
            : null;

        $pendingApproval = AbsenceDocument::where('status', 'pending')->count();

        $machines = [];
        $mesinOnline = 0;
        $mesinOffline = 0;
        $recentAttendances = collect();
        $pegawaiBelumPresensi = collect();

        /*
        |--------------------------------------------------------------------------
        | DATA MESIN
        |--------------------------------------------------------------------------
        */

        if (DB::getSchemaBuilder()->hasTable('attendance_machines')) {
            $machineRows = DB::table('attendance_machines')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $machines = $machineRows->map(function ($machine, $index) {
                $online = $index % 3 !== 0;

                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'status' => $online,
                    'label' => $online ? 'Terhubung' : 'Tidak Terhubung',
                ];
            })->toArray();

            $mesinOnline = collect($machines)->where('status', true)->count();
            $mesinOffline = collect($machines)->where('status', false)->count();
        }

        /*
        |--------------------------------------------------------------------------
        | DATA PRESENSI HARI INI
        |--------------------------------------------------------------------------
        */

        if (
            DB::getSchemaBuilder()->hasTable('attendance_logs') &&
            DB::getSchemaBuilder()->hasTable('employees')
        ) {
            $employeeIdsHadir = DB::table('attendance_logs')
                ->whereDate('created_at', $tanggal)
                ->distinct()
                ->pluck('employee_id');

            $hadirHariIni = $employeeIdsHadir->count();
            $belumPresensi = max($totalPegawai - $hadirHariIni, 0);

            $ditolakGeofence = DB::table('attendance_logs')
                ->whereDate('created_at', $tanggal)
                ->where('validation_reason', 'OUTSIDE_GEOFENCE')
                ->count();

            $ditolakWaktu = DB::table('attendance_logs')
                ->whereDate('created_at', $tanggal)
                ->where('validation_reason', 'TIME_NOT_SYNC')
                ->count();

            $validHariIni = DB::table('attendance_logs')
                ->whereDate('created_at', $tanggal)
                ->where('validation_status', 'VALID')
                ->count();

            $rejectedHariIni = DB::table('attendance_logs')
                ->whereDate('created_at', $tanggal)
                ->where('validation_status', 'REJECTED')
                ->count();

            $recentAttendances = DB::table('attendance_logs')
                ->join('employees', 'attendance_logs.employee_id', '=', 'employees.id')
                ->whereDate('attendance_logs.created_at', $tanggal)
                ->select(
                    'employees.name',
                    'employees.nip',
                    'attendance_logs.created_at',
                    'attendance_logs.validation_status',
                    'attendance_logs.validation_reason',
                )
                ->orderByDesc('attendance_logs.created_at')
                ->limit(8)
                ->get();

            $pegawaiBelumPresensi = Employee::when(
                    $employeeIdsHadir->isNotEmpty(),
                    fn ($q) => $q->whereNotIn('id', $employeeIdsHadir)
                )
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        */

        $summaryCards = [
            [
                'title' => 'Instansi / OPD',
                'value' => 'DINAS KOMUNIKASI DAN INFORMATIKA',
                'subtitle' => 'Unit aktif',
                'route' => route('superadmin.master.instansi.index'),
                'accent' => 'cyan',
            ],
            [
                'title' => 'Total Pegawai',
                'value' => $totalPegawai,
                'subtitle' => 'Data kepegawaian',
                'route' => route('superadmin.pegawai.index'),
                'accent' => 'sky',
            ],
            [
                'title' => 'User Aktif',
                'value' => $totalUserAktif,
                'subtitle' => "Dari {$totalUser} akun",
                'route' => route('superadmin.pengguna.index'),
                'accent' => 'emerald',
            ],
            [
                'title' => 'Hadir Hari Ini',
                'value' => $hadirHariIni,
                'subtitle' => "Tanggal {$tanggal}",
                'route' => route('superadmin.laporan.presensi-harian', ['tanggal' => $tanggal]),
                'accent' => 'indigo',
            ],
            [
                'title' => 'Pending Approval',
                'value' => $pendingApproval,
                'subtitle' => 'Menunggu persetujuan',
                'route' => route('superadmin.pegawai.ketidakhadiran'),
                'accent' => 'rose',
            ],
            [
                'title' => 'Ditolak Geofence',
                'value' => $ditolakGeofence,
                'subtitle' => 'Di luar radius',
                'route' => route('superadmin.laporan.presensi-harian', ['tanggal' => $tanggal]),
                'accent' => 'rose',
            ],

            [
                'title' => 'Waktu Tidak Sinkron',
                'value' => $ditolakWaktu,
                'subtitle' => 'Perbedaan waktu server',
                'route' => route('superadmin.laporan.presensi-harian', ['tanggal' => $tanggal]),
                'accent' => 'amber',
            ],
            [
                'title' => 'Presensi Valid',
                'value' => $validHariIni,
                'subtitle' => 'Validasi berhasil',
                'route' => route('superadmin.laporan.presensi-harian', ['tanggal'=>$tanggal]),
                'accent' => 'green',
            ],

            [
                'title' => 'Presensi Ditolak',
                'value' => $rejectedHariIni,
                'subtitle' => 'Semua alasan',
                'route' => route('superadmin.laporan.presensi-harian', ['tanggal'=>$tanggal]),
                'accent' => 'red',
            ],

        ];

        return view('superadmin.dashboard', compact(
            'tanggal',
            'summaryCards',
            'totalPegawai',
            'totalUser',
            'totalUserAktif',
            'hadirHariIni',
            'terlambatHariIni',
            'belumPresensi',
            'pendingKendala',
            'hariLiburTerdekat',
            'mesinOnline',
            'mesinOffline',
            'machines',
            'recentAttendances',
            'pegawaiBelumPresensi'
        ));
    }
}