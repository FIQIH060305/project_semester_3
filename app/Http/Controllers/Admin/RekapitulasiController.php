<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\RekapBulanan;
use Illuminate\Http\Request;

class RekapitulasiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        // Hitung ulang & simpan ke rekap_bulanan untuk setiap akun aktif
        $akunAktif = Akun::where('status_akun', 'aktif')->get();

        foreach ($akunAktif as $akun) {
            $riwayat = $akun->riwayatOlahraga()
                ->whereMonth('tanggal_waktu', $bulan)
                ->whereYear('tanggal_waktu', $tahun)
                ->get();

            $rataRataBerat = $akun->riwayatBeratBadan()
                ->whereMonth('tanggal_pencatatan', $bulan)
                ->whereYear('tanggal_pencatatan', $tahun)
                ->avg('berat_badan');

            RekapBulanan::updateOrCreate(
                ['id_akun' => $akun->id_akun, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'total_jarak_km' => $riwayat->sum('jarak_km'),
                    'total_kalori_terbakar' => $riwayat->sum('kalori_terbakar'),
                    'total_langkah' => $riwayat->sum('jumlah_langkah'),
                    'rata_rata_berat_badan' => $rataRataBerat ? round($rataRataBerat, 1) : null,
                ]
            );
        }

        // Baca dari tabel rekap_bulanan (bukan hitung ulang lagi)
        $rekapList = RekapBulanan::with('akun')
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->get();

        $totalSesi    = 0; // dihitung terpisah karena jumlah sesi tidak disimpan di rekap_bulanan
        foreach ($akunAktif as $akun) {
            $totalSesi += $akun->riwayatOlahraga()
                ->whereMonth('tanggal_waktu', $bulan)->whereYear('tanggal_waktu', $tahun)->count();
        }

        $totalJarak   = $rekapList->sum('total_jarak_km');
        $totalKalori  = $rekapList->sum('total_kalori_terbakar');
        $totalLangkah = $rekapList->sum('total_langkah');

        return view('admin.rekapan.index', compact(
            'bulan', 'tahun', 'totalSesi', 'totalJarak', 'totalKalori', 'totalLangkah', 'rekapList'
        ));
    }
}