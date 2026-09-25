<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $akun = Auth::guard('web')->user();

        // Mengambil input dari filter dropdown (jika tidak ada, gunakan bulan & tahun saat ini)
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        // Filter riwayat berdasarkan bulan & tahun pilihan user
        $riwayatBulanIni = $akun->riwayatOlahraga()
            ->whereMonth('tanggal_waktu', $bulan)
            ->whereYear('tanggal_waktu', $tahun)
            ->orderByDesc('tanggal_waktu')
            ->get();

        $totalJarak = $riwayatBulanIni->sum('jarak_km');
        $totalKalori = $riwayatBulanIni->sum('kalori_terbakar');
        $totalLangkah = $riwayatBulanIni->sum('jumlah_langkah');

        return view('user.rekap.index', compact(
            'bulan', 
            'tahun', 
            'riwayatBulanIni', 
            'totalJarak', 
            'totalKalori', 
            'totalLangkah'
        ));
    }
}