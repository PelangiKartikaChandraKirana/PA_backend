<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

// ADMIN
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\VerificationController;

// SUPERADMIN
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\PegawaiController;
use App\Http\Controllers\SuperAdmin\LaporanController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\LaporanTppController;
use App\Http\Controllers\SuperAdmin\Master\TppController;

// ABSENSI
use App\Http\Controllers\SuperAdmin\Absensi\WeeklyScheduleCategoryController;
use App\Http\Controllers\SuperAdmin\Absensi\WeeklyScheduleController;
use App\Http\Controllers\SuperAdmin\Absensi\CompanyLocationController;
use App\Http\Controllers\SuperAdmin\Absensi\UserDeviceController;
use App\Http\Controllers\SuperAdmin\Absensi\LocationController;
use App\Http\Controllers\SuperAdmin\Absensi\EmployeeLocationController;
use App\Http\Controllers\SuperAdmin\Absensi\MachineFaultController;
use App\Http\Controllers\SuperAdmin\Absensi\MachineController;
use App\Http\Controllers\SuperAdmin\Absensi\AttendanceLogController;

// MASTER / KONFIGURASI
use App\Http\Controllers\SuperAdmin\Master\DocumentTypeController;
use App\Http\Controllers\SuperAdmin\Master\EmployeeTypeController;
use App\Http\Controllers\SuperAdmin\Master\HolidayController;
use App\Http\Controllers\SuperAdmin\Master\MachineFaultTypeController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $role = Auth::user()->role;

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }

    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications/count', function () {
        return response()->json(['count' => auth()->user()->unreadNotifications->count()]);
    })->name('notifications.count');

    Route::get('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['action_url'] ?? route('dashboard'));
    })->name('notifications.read');

    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-as-read');
});

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // KEPEGAWAIAN
        Route::resource('pegawai', PegawaiController::class);

        Route::get('pegawai-wajah', [PegawaiController::class, 'wajah'])->name('pegawai.wajah');
        Route::post('pegawai/{pegawai}/wajah', [PegawaiController::class, 'storeWajah'])->name('pegawai.wajah.store');
        Route::delete('wajah/{face}', [PegawaiController::class, 'deleteWajah'])->name('pegawai.wajah.delete');
        Route::patch('wajah/{face}/aktif', [PegawaiController::class, 'setAktif'])->name('pegawai.wajah.aktif');

        Route::get('ketidakhadiran', [PegawaiController::class, 'ketidakhadiran'])->name('pegawai.ketidakhadiran');
        Route::patch('ketidakhadiran/{document}', [PegawaiController::class, 'updateDocumentStatus'])->name('pegawai.document.update');

        Route::post('pegawai-wajah/sync-all', [PegawaiController::class, 'syncAllWajah'])
            ->name('pegawai.wajah.sync-all');

        // PENGGUNA
        Route::get('pengguna', [PegawaiController::class, 'pengguna'])->name('pengguna.index');
        Route::get('pengguna/create', [PegawaiController::class, 'createPengguna'])->name('pengguna.create');
        Route::post('pengguna', [PegawaiController::class, 'storePengguna'])->name('pengguna.store');
        Route::get('pengguna/export', [PegawaiController::class, 'exportPengguna'])->name('pengguna.export');
        Route::post('pengguna/import', [PegawaiController::class, 'importPengguna'])->name('pengguna.import');
        Route::patch('pengguna/bulk-status', [PegawaiController::class, 'bulkStatus'])->name('pengguna.bulk-status');
        Route::get('pengguna/{user}/edit', [PegawaiController::class, 'editPengguna'])->name('pengguna.edit');
        Route::put('pengguna/{user}', [PegawaiController::class, 'updatePengguna'])->name('pengguna.update');
        Route::patch('pengguna/{user}/reset', [PegawaiController::class, 'resetPassword'])->name('pengguna.reset');
        Route::patch('pengguna/{user}/status', [PegawaiController::class, 'toggleStatus'])->name('pengguna.status');
        Route::get('pengguna/{user}/riwayat-login', [PegawaiController::class, 'riwayatLogin'])->name('pengguna.riwayat-login');
        Route::get('pengguna/{user}/perangkat', [PegawaiController::class, 'perangkat'])->name('pengguna.perangkat');
        Route::get('pengguna/{user}/lokasi-absen', [PegawaiController::class, 'lokasiAbsen'])->name('pengguna.lokasi-absen');
        Route::get('pengguna/{user}/detail-akses', [PegawaiController::class, 'detailAkses'])->name('pengguna.detail-akses');
        Route::delete('pengguna/{user}', [PegawaiController::class, 'destroyPengguna'])->name('pengguna.delete');

        // ABSENSI
        Route::resource('absensi/kategori-jadwal-kerja', WeeklyScheduleCategoryController::class)
            ->names('absensi.kategori-jadwal-kerja');

        Route::resource('absensi/jadwal-kerja', WeeklyScheduleController::class)
            ->names('absensi.jadwal-kerja');

        Route::resource('absensi/lokasi-absen-instansi', CompanyLocationController::class)
            ->names('absensi.lokasi-absen-instansi');

        Route::resource('absensi/lokasi-absen', LocationController::class)
            ->names('absensi.lokasi-absen');

        Route::resource('absensi/perangkat-pengguna', UserDeviceController::class)
            ->names('absensi.perangkat-pengguna');

        Route::get('absensi/lokasi-absen-pegawai', [EmployeeLocationController::class, 'index'])
            ->name('absensi.lokasi-absen-pegawai.index');
        Route::get('absensi/lokasi-absen-pegawai/{employee}', [EmployeeLocationController::class, 'show'])
            ->name('absensi.lokasi-absen-pegawai.show');
        Route::post('absensi/lokasi-absen-pegawai/bulk', [EmployeeLocationController::class, 'bulkStore'])
            ->name('absensi.lokasi-absen-pegawai.bulk-store');
        Route::post('absensi/lokasi-absen-pegawai', [EmployeeLocationController::class, 'store'])
            ->name('absensi.lokasi-absen-pegawai.store');

        Route::resource('absensi/lapor-kendala-absensi', MachineFaultController::class)
            ->names('absensi.lapor-kendala-absensi');

        Route::resource('absensi/mesin', MachineController::class)
            ->names('absensi.mesin');

        Route::get('absensi/riwayat-presensi', [AttendanceLogController::class, 'index'])
            ->name('absensi.riwayat-presensi.index');

        // LAPORAN
        Route::get('laporan/presensi-harian', [LaporanController::class, 'presensiHarian'])
            ->name('laporan.presensi-harian');

        Route::get('laporan/presensi-bulanan', [LaporanController::class, 'presensiBulanan'])
            ->name('laporan.presensi-bulanan');

        Route::get('laporan/tpp', [LaporanTppController::class, 'index'])
            ->name('laporan.tpp');

        // MASTER
        Route::resource('master/tipe-dokumen', DocumentTypeController::class)
            ->names('master.tipe-dokumen')
            ->parameters(['tipe-dokumen' => 'tipe_dokumen']);

        Route::resource('master/instansi', CompanyController::class)
            ->names('master.instansi');

        Route::resource('master/tipe-kendala', MachineFaultTypeController::class)
            ->names('master.tipe-kendala')
            ->parameters(['tipe-kendala' => 'tipe_kendala']);

        Route::resource('master/tpp', TppController::class)
            ->names('master.tpp')
            ->only(['index', 'update']);

        Route::resource('tipe-pegawai', EmployeeTypeController::class)->except('show');
        Route::resource('hari-libur', HolidayController::class)->except('show');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/presensi', [AdminDashboardController::class, 'presensi'])->name('presensi');
        Route::get('/pegawai', [AdminDashboardController::class, 'pegawai'])->name('pegawai');
        Route::get('/pegawai/{id}', [AdminDashboardController::class, 'showPegawai'])->name('pegawai.show');
        Route::get('/monitoring', [AdminDashboardController::class, 'monitoring'])->name('monitoring');
        Route::get('/monitoring/export', [AdminDashboardController::class, 'exportMonitoring'])->name('monitoring.export');

        // VERIFIKASI
        Route::get('/verifikasi/izin', [VerificationController::class, 'index'])->name('verifikasi.izin');
        Route::post('/verifikasi/izin/{document}/approve', [VerificationController::class, 'approveIzin'])->name('verifikasi.izin.approve');
        Route::post('/verifikasi/izin/{document}/reject', [VerificationController::class, 'rejectIzin'])->name('verifikasi.izin.reject');

        Route::get('/verifikasi/kendala', [VerificationController::class, 'kendala'])->name('verifikasi.kendala');
        Route::post('/verifikasi/kendala/{report}/approve', [VerificationController::class, 'approveKendala'])->name('verifikasi.kendala.approve');
        Route::post('/verifikasi/kendala/{report}/reject', [VerificationController::class, 'rejectKendala'])->name('verifikasi.kendala.reject');
    });

require __DIR__ . '/auth.php';