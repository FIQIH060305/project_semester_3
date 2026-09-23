<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\JadwalOlahraga;
use App\Models\KontenEdukasi;
use App\Models\RiwayatOlahraga;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalPengguna   = Akun::count();
        $totalAkunAktif  = Akun::where('status_akun', 'aktif')->count();

        $totalKonten     = KontenEdukasi::count();
        $kontenPerTipe   = KontenEdukasi::selectRaw('tipe_konten, count(*) as total')
            ->groupBy('tipe_konten')->pluck('total', 'tipe_konten');

        $totalJadwal     = JadwalOlahraga::count();
        $jadwalHariIni   = JadwalOlahraga::whereDate('tanggal_tugas', today())->count();

        $totalRiwayat    = RiwayatOlahraga::count();

        return view('admin.dashboard', compact(
            'totalPengguna', 'totalAkunAktif', 'totalKonten',
            'kontenPerTipe', 'totalJadwal', 'jadwalHariIni', 'totalRiwayat'
        ));
    }
}