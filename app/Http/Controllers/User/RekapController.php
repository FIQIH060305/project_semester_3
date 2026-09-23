<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RekapController extends Controller
{
    public function index()
    {
        $akun = Auth::guard('web')->user();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $riwayatBulanIni = $akun->riwayatOlahraga()
            ->whereMonth('tanggal_waktu', $bulanIni)
            ->whereYear('tanggal_waktu', $tahunIni)
            ->orderByDesc('tanggal_waktu')
            ->get();

        $totalJarak = $riwayatBulanIni->sum('jarak_km');
        $totalKalori = $riwayatBulanIni->sum('kalori_terbakar');
        $totalLangkah = $riwayatBulanIni->sum('jumlah_langkah');

        return view('user.rekap.index', compact(
            'riwayatBulanIni', 'totalJarak', 'totalKalori', 'totalLangkah'
        ));
    }
}