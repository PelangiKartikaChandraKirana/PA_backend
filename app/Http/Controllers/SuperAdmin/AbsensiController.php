<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class AbsensiController extends Controller
{
    public function kategoriJadwalKerja()
    {
        return view('superadmin.absensi.kategori-jadwal-kerja.index');
    }

    public function jadwalKerja()
    {
        return view('superadmin.absensi.jadwal-kerja.index');
    }

    public function lokasiAbsenInstansi()
    {
        return view('superadmin.absensi.lokasi-absen-instansi.index');
    }

    public function perangkatPengguna()
    {
        return view('superadmin.absensi.perangkat-pengguna.index');
    }

    public function lokasiAbsenPegawai()
    {
        return view('superadmin.absensi.lokasi-absen-pegawai.index');
    }

    public function lokasiAbsen()
    {
        return view('superadmin.absensi.lokasi-absen.index');
    }

    public function laporKendalaAbsensi()
    {
        return view('superadmin.absensi.lapor-kendala-absensi.index');
    }

    public function riwayatPresensi()
    {
        return view('superadmin.absensi.riwayat-presensi.index');
    }

    public function mesin()
    {
        return view('superadmin.absensi.mesin.index');
    }
}