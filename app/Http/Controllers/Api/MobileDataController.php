<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalOlahraga;
use App\Models\JenisOlahraga;
use App\Models\KontenEdukasi;
use App\Models\Notifikasi;
use App\Models\RiwayatOlahraga;
use App\Models\Target;
use App\Support\Antropometri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileDataController extends Controller
{
    private function resolveFotoUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        return str_starts_with($path, 'http')
            ? $path
            : asset('storage/' . ltrim($path, '/'));
    }

    /**
     * 1. DATA DASBOR & STATISTIK 7 HARI TERAKHIR
     */
    public function dashboard(Request $request)
    {
        $akun = $request->user();
        $target = $akun->targetAktif;

        $beratTerakhir = $akun->riwayatBeratBadan()
            ->latest('tanggal_pencatatan')
            ->latest('id_log_berat')
            ->first();

        $beratSekarang = $beratTerakhir
            ? (float) $beratTerakhir->berat_badan
            : (float) $akun->berat_badan_awal;

        $totalLangkahHariIni = (int) $akun->riwayatOlahraga()
            ->whereDate('tanggal_waktu', today())
            ->sum('jumlah_langkah');

        $totalJarakHariIni = round((float) $akun->riwayatOlahraga()
            ->whereDate('tanggal_waktu', today())
            ->sum('jarak_km'), 2);

        $totalKaloriHariIni = round((float) $akun->riwayatOlahraga()
            ->whereDate('tanggal_waktu', today())
            ->sum('kalori_terbakar'), 1);

        $progresTarget = 0;
        $sudahTurun = 0;
        if ($target) {
            $totalHarusTurun = $target->berat_awal - $target->berat_target;
            $sudahTurun = round($target->berat_awal - $beratSekarang, 1);
            if ($totalHarusTurun > 0) {
                $progresTarget = max(0, min(100, (int) round(($sudahTurun / $totalHarusTurun) * 100)));
            }
        }

        $totalSesiBulanIni = $akun->riwayatOlahraga()
            ->whereMonth('tanggal_waktu', now()->month)
            ->whereYear('tanggal_waktu', now()->year)
            ->count();

        $grafik7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = today()->subDays($i);
            $grafik7Hari[] = [
                'hari'     => $tanggal->translatedFormat('D'),
                'tanggal'  => $tanggal->toDateString(),
                'jarak_km' => round((float) $akun->riwayatOlahraga()->whereDate('tanggal_waktu', $tanggal)->sum('jarak_km'), 2),
                'kalori'   => round((float) $akun->riwayatOlahraga()->whereDate('tanggal_waktu', $tanggal)->sum('kalori_terbakar'), 1),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id_akun'          => $akun->id_akun,
                    'nama_lengkap'     => $akun->nama_lengkap,
                    'email'            => $akun->email,
                    'tinggi_badan'     => (float) $akun->tinggi_badan,
                    'berat_sekarang'   => $beratSekarang,
                    'foto_profil'      => $this->resolveFotoUrl($akun->foto_profil),
                ],
                'target' => $target ? [
                    'berat_awal'     => (float) $target->berat_awal,
                    'berat_target'   => (float) $target->berat_target,
                    'progres_persen' => $progresTarget,
                    'sudah_turun'    => $sudahTurun,
                ] : null,
                'hari_ini' => [
                    'langkah'  => $totalLangkahHariIni,
                    'jarak_km' => $totalJarakHariIni,
                    'kalori'   => $totalKaloriHariIni,
                ],
                'total_sesi_bulan_ini' => $totalSesiBulanIni,
                'grafik_7_hari'        => $grafik7Hari,
            ],
        ]);
    }

    /**
     * 2. AMBIL RIWAYAT OLAHRAGA USER
     */
    public function indexAktivitas(Request $request)
    {
        $akun = $request->user();

        $riwayat = RiwayatOlahraga::with('jenisOlahraga')
            ->where('id_akun', $akun->id_akun)
            ->orderByDesc('tanggal_waktu')
            ->get()
            ->map(function ($item) {
                $parts = explode(':', (string) ($item->durasi_waktu ?? '00:00:00'));
                $detik = ((int) ($parts[0] ?? 0) * 3600)
                    + ((int) ($parts[1] ?? 0) * 60)
                    + (int) ($parts[2] ?? 0);

                return [
                    'id_aktivitas'    => $item->id_riwayat,
                    'id_riwayat'      => $item->id_riwayat,
                    'jenis_olahraga'  => $item->jenisOlahraga->nama_jenis ?? 'Berlari',
                    'jarak_km'        => (float) $item->jarak_km,
                    'durasi_detik'    => max(1, $detik),
                    'durasi_waktu'    => $item->durasi_waktu,
                    'jumlah_langkah'  => (int) $item->jumlah_langkah,
                    'kalori_terbakar' => (float) $item->kalori_terbakar,
                    'created_at'      => $item->tanggal_waktu,
                    'tanggal_waktu'   => $item->tanggal_waktu,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $riwayat,
        ]);
    }

    /**
     * 3. SIMPAN HASIL PELACAKAN OLAHRAGA DARI MOBILE KE DATABASE
     */
    public function storeAktivitas(Request $request)
    {
        $akun = $request->user();

        $validated = $request->validate([
            'jenis_olahraga'  => ['nullable', 'string'],
            'jarak_km'        => ['required', 'numeric', 'min:0'],
            'durasi_detik'    => ['required', 'integer', 'min:1'],
            'jumlah_langkah'  => ['required', 'integer', 'min:0'],
            'kalori_terbakar' => ['required', 'numeric', 'min:0'],
            'id_jadwal'       => ['nullable', 'integer'],
        ]);

        $namaJenis = $validated['jenis_olahraga'] ?? 'Lari';
        $jenis = JenisOlahraga::where('nama_jenis', 'like', "%{$namaJenis}%")->first()
            ?? JenisOlahraga::first();

        if (!$jenis) {
            $jenis = JenisOlahraga::create([
                'nama_jenis' => 'Lari',
                'met'        => 8.0,
            ]);
        }

        $durasiFormat = gmdate('H:i:s', (int) $validated['durasi_detik']);

        $riwayat = RiwayatOlahraga::create([
            'id_akun'         => $akun->id_akun,
            'id_jenis'        => $jenis->id_jenis,
            'id_jadwal'       => $validated['id_jadwal'] ?? null,
            'jarak_km'        => round((float) $validated['jarak_km'], 2),
            'durasi_waktu'    => $durasiFormat,
            'jumlah_langkah'  => (int) $validated['jumlah_langkah'],
            'kalori_terbakar' => round((float) $validated['kalori_terbakar'], 1),
            'tanggal_waktu'   => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi olahraga berhasil disimpan ke server!',
            'data'    => $riwayat,
        ], 201);
    }

    /**
     * 4. UPDATE PROFIL, FOTO PROFIL & CATAT RIWAYAT BERAT BADAN BARU
     */
    public function updateProfile(Request $request)
    {
        $akun = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => ['nullable', 'string', 'max:100'],
            'tinggi_badan' => ['required', 'numeric', 'min:140', 'max:200'],
            'berat_badan'  => ['required', 'numeric', 'min:30', 'max:250'],
            'berat_target' => ['required', 'numeric', 'lt:berat_badan'],
            'foto_profil'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'hapus_foto'   => ['nullable', 'boolean'],
        ]);

        $tinggi = (float) $validated['tinggi_badan'];
        $berat  = (float) $validated['berat_badan'];
        $target = (float) $validated['berat_target'];

        try {
            Antropometri::pastikanTargetWajar($tinggi, $berat, $target, 'berat_target');
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $updateData = ['tinggi_badan' => $tinggi];
        if (!empty($validated['nama_lengkap'])) {
            $updateData['nama_lengkap'] = $validated['nama_lengkap'];
        }

        // Proses unggah foto profil baru dari HP
        if ($request->hasFile('foto_profil')) {
            if (!empty($akun->foto_profil) && Storage::disk('public')->exists($akun->foto_profil)) {
                Storage::disk('public')->delete($akun->foto_profil);
            }
            $storedPath = $request->file('foto_profil')->store('foto_profil', 'public');
            $updateData['foto_profil'] = $storedPath;
        } elseif ($request->boolean('hapus_foto')) {
            if (!empty($akun->foto_profil) && Storage::disk('public')->exists($akun->foto_profil)) {
                Storage::disk('public')->delete($akun->foto_profil);
            }
            $updateData['foto_profil'] = null;
        }

        $akun->update($updateData);

        $akun->riwayatBeratBadan()->create([
            'berat_badan'        => $berat,
            'bmi'                => Antropometri::imt($berat, $tinggi),
            'tanggal_pencatatan' => today(),
        ]);

        $targetAktif = $akun->targetAktif;
        if ($targetAktif) {
            $targetAktif->update(['berat_target' => $target]);
        } else {
            Target::create([
                'id_akun'       => $akun->id_akun,
                'berat_awal'    => $berat,
                'berat_target'  => $target,
                'tanggal_mulai' => today(),
                'status'        => 'aktif',
            ]);
        }

        $freshAkun = $akun->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Profil & foto berhasil diperbarui!',
            'user'    => [
                'id_akun'          => $freshAkun->id_akun,
                'nama_lengkap'     => $freshAkun->nama_lengkap,
                'email'            => $freshAkun->email,
                'jenis_kelamin'    => $freshAkun->jenis_kelamin,
                'usia'             => $freshAkun->usia,
                'tinggi_badan'     => (float) $freshAkun->tinggi_badan,
                'berat_sekarang'   => $berat,
                'foto_profil'      => $this->resolveFotoUrl($freshAkun->foto_profil),
                'target_aktif'     => $freshAkun->targetAktif,
            ],
        ]);
    }

    /**
     * 5. JADWAL OLAHRAGA, KONTEN EDUKASI & NOTIFIKASI
     */
    public function jadwal(Request $request)
    {
        $jadwal = JadwalOlahraga::with('jenisOlahraga')
            ->where('id_akun', $request->user()->id_akun)
            ->orderBy('tanggal_jadwal')
            ->get();

        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    public function edukasi()
    {
        $konten = KontenEdukasi::latest()->get();
        return response()->json(['success' => true, 'data' => $konten]);
    }

    public function notifikasi(Request $request)
    {
        $akunId = $request->user()->id_akun;
        $notif = Notifikasi::whereHas('jadwal', function ($q) use ($akunId) {
                $q->where('id_akun', $akunId);
            })
            ->latest('waktu_kirim')
            ->get();

        return response()->json(['success' => true, 'data' => $notif]);
    }
}