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

        $notifikasiBelumDibaca = Notifikasi::whereHas('jadwal', function ($q) use ($akun) {
                $q->where('id_akun', $akun->id_akun);
            })
            ->where('status_dibaca', false)
            ->latest('waktu_kirim')
            ->get();

        // ===== STATISTIK BARU =====

        // 1. Progres menuju target berat badan (0-100%)
        $progresTarget = 0;
        $sudahTurun = 0;
        if ($target && $beratTerakhir) {
            $totalHarusTurun = $target->berat_awal - $target->berat_target;
            $sudahTurun = round($target->berat_awal - $beratTerakhir->berat_badan, 1);

            if ($totalHarusTurun > 0) {
                $progresTarget = max(0, min(100, round($sudahTurun / $totalHarusTurun * 100)));
            }
        }

        // 2. Jumlah sesi olahraga bulan ini
        $totalSesiBulanIni = $akun->riwayatOlahraga()
            ->whereMonth('tanggal_waktu', now()->month)
            ->whereYear('tanggal_waktu', now()->year)
            ->count();

        // 3. Data grafik jarak 7 hari terakhir
        $labels7Hari = [];
        $data7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = today()->subDays($i);
            $labels7Hari[] = $tanggal->translatedFormat('D');
            $data7Hari[] = round(
                $akun->riwayatOlahraga()->whereDate('tanggal_waktu', $tanggal)->sum('jarak_km'),
                2
            );
        }

        return view('user.dashboard', compact(
            'akun', 'target', 'beratTerakhir',
            'totalLangkahHariIni', 'totalJarakHariIni', 'totalKaloriHariIni',
            'notifikasiBelumDibaca',
            'progresTarget', 'sudahTurun', 'totalSesiBulanIni',
            'labels7Hari', 'data7Hari'
        ));
    }
}