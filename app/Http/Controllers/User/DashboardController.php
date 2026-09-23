<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $akun = Auth::guard('web')->user();

        $target = $akun->targetAktif;

        $beratTerakhir = $akun->riwayatBeratBadan()
            ->latest('tanggal_pencatatan')->first();

        $totalLangkahHariIni = $akun->riwayatOlahraga()
            ->whereDate('tanggal_waktu', today())->sum('jumlah_langkah');

        $totalJarakHariIni = $akun->riwayatOlahraga()
            ->whereDate('tanggal_waktu', today())->sum('jarak_km');

        $totalKaloriHariIni = $akun->riwayatOlahraga()
            ->whereDate('tanggal_waktu', today())->sum('kalori_terbakar');

        // Notifikasi belum dibaca, khusus jadwal milik akun ini
        $notifikasiBelumDibaca = Notifikasi::whereHas('jadwal', function ($q) use ($akun) {
                $q->where('id_akun', $akun->id_akun);
            })
            ->where('status_dibaca', false)
            ->latest('waktu_kirim')
            ->get();

        return view('user.dashboard', compact(
            'akun', 'target', 'beratTerakhir',
            'totalLangkahHariIni', 'totalJarakHariIni', 'totalKaloriHariIni',
            'notifikasiBelumDibaca'
        ));
    }
}