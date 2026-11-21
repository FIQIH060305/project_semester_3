<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalOlahraga;
use App\Models\JenisOlahraga;
use App\Models\KontenEdukasi;
use App\Models\Notifikasi;
use App\Models\RiwayatBeratBadan;
use App\Models\RiwayatOlahraga;
use App\Models\Target;
use App\Support\Antropometri;
use Carbon\Carbon;
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
     * Helper: Simpan/perbarui 1 catatan berat badan per tanggal (anti-duplikat)
     */
    private function upsertBeratHarian($akun, float $berat, float $tinggi, string $tanggalStr): RiwayatBeratBadan
    {
        $bmi = Antropometri::imt($berat, $tinggi);

        $existingRows = RiwayatBeratBadan::where('id_akun', $akun->id_akun)
            ->whereDate('tanggal_pencatatan', $tanggalStr)
            ->orderByDesc('id_log_berat')
            ->get();

        if ($existingRows->isNotEmpty()) {
            $primary = $existingRows->first();
            $primary->update([
                'berat_badan' => round($berat, 2),
                'bmi'         => $bmi,
            ]);

            if ($existingRows->count() > 1) {
                $duplicateIds = $existingRows->skip(1)->pluck('id_log_berat');
                RiwayatBeratBadan::whereIn('id_log_berat', $duplicateIds)->delete();
            }

            return $primary->fresh();
        }

        return RiwayatBeratBadan::create([
            'id_akun'            => $akun->id_akun,
            'berat_badan'        => round($berat, 2),
            'bmi'                => $bmi,
            'tanggal_pencatatan' => $tanggalStr,
        ]);
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
                    'id_akun'        => $akun->id_akun,
                    'nama_lengkap'   => $akun->nama_lengkap,
                    'email'          => $akun->email,
                    'tinggi_badan'   => (float) $akun->tinggi_badan,
                    'berat_sekarang' => $beratSekarang,
                    'foto_profil'    => $this->resolveFotoUrl($akun->foto_profil),
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
     * 1B. PEMANTAUAN PROGRES BERAT BADAN MINGGUAN (SENIN - MINGGU)
     */
    public function beratBadanMingguan(Request $request)
    {
        $akun = $request->user();
        $tz = 'Asia/Jakarta';
        $nowLocal = Carbon::now($tz);

        $weekOffset = (int) $request->query('week_offset', 0);
        if ($weekOffset > 0) {
            $weekOffset = 0;
        }

        $startOfWeek = $nowLocal->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->addWeeks($weekOffset)
            ->startOfDay();
        $endOfWeek = $startOfWeek->copy()
            ->endOfWeek(Carbon::SUNDAY)
            ->endOfDay();

        $startDateStr = $startOfWeek->toDateString();
        $endDateStr   = $endOfWeek->toDateString();

        // Cek apakah user pernah punya catatan berat badan
        $totalSemuaCatatan = RiwayatBeratBadan::where('id_akun', $akun->id_akun)->count();

        // Berat valid terakhir SEBELUM Senin minggu ini (menjadi baseline otomatis minggu baru)
        $catatanSebelumMinggu = RiwayatBeratBadan::where('id_akun', $akun->id_akun)
            ->whereDate('tanggal_pencatatan', '<', $startDateStr)
            ->orderByDesc('tanggal_pencatatan')
            ->orderByDesc('id_log_berat')
            ->first();

        $baselineSebelumMinggu = $catatanSebelumMinggu
            ? (float) $catatanSebelumMinggu->berat_badan
            : null;

        // Ambil seluruh catatan pada minggu terpilih (1 data terbaru per tanggal)
        $catatanMingguIni = RiwayatBeratBadan::where('id_akun', $akun->id_akun)
            ->whereDate('tanggal_pencatatan', '>=', $startDateStr)
            ->whereDate('tanggal_pencatatan', '<=', $endDateStr)
            ->orderBy('tanggal_pencatatan')
            ->orderByDesc('id_log_berat')
            ->get()
            ->unique(function ($item) {
                return Carbon::parse($item->tanggal_pencatatan)->toDateString();
            })
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal_pencatatan)->toDateString();
            });

        // Ambil seluruh aktivitas olahraga pada minggu terpilih untuk korelasi
        $aktivitasMingguIni = RiwayatOlahraga::where('id_akun', $akun->id_akun)
            ->whereDate('tanggal_waktu', '>=', $startDateStr)
            ->whereDate('tanggal_waktu', '<=', $endDateStr)
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_waktu)->toDateString();
            });

        $namaHariPendek = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $namaHariLengkap = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        $lastValidWeight = $baselineSebelumMinggu;
        $beratAwalMinggu = $baselineSebelumMinggu;
        $beratTerakhirMinggu = null;
        $jumlahHariDitimbang = 0;

        $totalLangkahMinggu = 0;
        $totalJarakMinggu = 0.0;
        $totalKaloriMinggu = 0.0;
        $totalDurasiDetikMinggu = 0;
        $hariAktifOlahraga = 0;

        $hariList = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDay = $startOfWeek->copy()->addDays($i);
            $dateStr = $currentDay->toDateString();
            $isToday = $dateStr === $nowLocal->toDateString();
            $isFuture = $currentDay->gt($nowLocal->copy()->endOfDay());

            // Hitung aktivitas hari tersebut
            $logsAktivitas = $aktivitasMingguIni->get($dateStr, collect());
            $langkahHari = (int) $logsAktivitas->sum('jumlah_langkah');
            $jarakHari = round((float) $logsAktivitas->sum('jarak_km'), 2);
            $kaloriHari = round((float) $logsAktivitas->sum('kalori_terbakar'), 1);
            $sesiHari = $logsAktivitas->count();

            $detikHari = 0;
            foreach ($logsAktivitas as $act) {
                $parts = explode(':', (string) ($act->durasi_waktu ?? '00:00:00'));
                $detikHari += ((int) ($parts[0] ?? 0) * 3600)
                    + ((int) ($parts[1] ?? 0) * 60)
                    + (int) ($parts[2] ?? 0);
            }

            if ($sesiHari > 0 || $langkahHari > 0) {
                $hariAktifOlahraga++;
            }

            $totalLangkahMinggu += $langkahHari;
            $totalJarakMinggu += $jarakHari;
            $totalKaloriMinggu += $kaloriHari;
            $totalDurasiDetikMinggu += $detikHari;

            // Hitung berat badan & selisih harian
            $recordBerat = $catatanMingguIni->get($dateStr);
            $beratHari = null;
            $selisihKg = null;
            $selisihGram = null;
            $arah = 'kosong';

            if ($recordBerat) {
                $beratHari = round((float) $recordBerat->berat_badan, 2);
                $jumlahHariDitimbang++;

                if ($beratAwalMinggu === null) {
                    $beratAwalMinggu = $beratHari;
                }

                if ($lastValidWeight !== null) {
                    $selisihKg = round($beratHari - $lastValidWeight, 3);
                    $selisihGram = (int) round($selisihKg * 1000);
                    if ($selisihGram < 0) {
                        $arah = 'turun';
                    } elseif ($selisihGram > 0) {
                        $arah = 'naik';
                    } else {
                        $arah = 'tetap';
                    }
                } else {
                    $selisihKg = 0.0;
                    $selisihGram = 0;
                    $arah = 'awal';
                }

                $lastValidWeight = $beratHari;
                $beratTerakhirMinggu = $beratHari;
            }

            $hariList[] = [
                'hari_pendek'  => $namaHariPendek[$i],
                'hari_lengkap' => $namaHariLengkap[$i],
                'tanggal'      => $dateStr,
                'tanggal_label'=> $currentDay->translatedFormat('d M'),
                'is_today'     => $isToday,
                'is_future'    => $isFuture,
                'berat_badan'  => $beratHari,
                'selisih_kg'   => $selisihKg,
                'selisih_gram' => $selisihGram,
                'arah'         => $arah,
                'aktivitas'    => [
                    'langkah'      => $langkahHari,
                    'jarak_km'     => $jarakHari,
                    'kalori'       => $kaloriHari,
                    'durasi_menit' => (int) round($detikHari / 60),
                    'jumlah_sesi'  => $sesiHari,
                ],
            ];
        }

        // Jika minggu ini belum ada penimbangan baru, berat akhir referensi menggunakan awal minggu (baseline)
        if ($beratTerakhirMinggu === null) {
            $beratTerakhirMinggu = $beratAwalMinggu;
        }

        $totalPerubahanKg = 0.0;
        $totalPerubahanGram = 0;
        $arahMingguan = 'tetap';

        if ($beratAwalMinggu !== null && $beratTerakhirMinggu !== null && $jumlahHariDitimbang > 0) {
            $totalPerubahanKg = round($beratTerakhirMinggu - $beratAwalMinggu, 3);
            $totalPerubahanGram = (int) round($totalPerubahanKg * 1000);
            if ($totalPerubahanGram < 0) {
                $arahMingguan = 'turun';
            } elseif ($totalPerubahanGram > 0) {
                $arahMingguan = 'naik';
            }
        }

        // Bangun riwayat ringkasan minggu-minggu sebelumnya (maks 8 minggu terakhir)
        $earliestRecord = RiwayatBeratBadan::where('id_akun', $akun->id_akun)
            ->orderBy('tanggal_pencatatan')
            ->first();

        $riwayatMingguan = [];
        $earliestWeekStart = $earliestRecord
            ? Carbon::parse($earliestRecord->tanggal_pencatatan, $tz)->startOfWeek(Carbon::MONDAY)->startOfDay()
            : $nowLocal->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();

        for ($w = 0; $w >= -7; $w--) {
            $wStart = $nowLocal->copy()->startOfWeek(Carbon::MONDAY)->addWeeks($w)->startOfDay();
            $wEnd   = $wStart->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

            if ($w < 0 && $wEnd->lt($earliestWeekStart)) {
                break;
            }

            $wStartStr = $wStart->toDateString();
            $wEndStr   = $wEnd->toDateString();

            $prevW = RiwayatBeratBadan::where('id_akun', $akun->id_akun)
                ->whereDate('tanggal_pencatatan', '<', $wStartStr)
                ->orderByDesc('tanggal_pencatatan')
                ->orderByDesc('id_log_berat')
                ->first();

            $logsW = RiwayatBeratBadan::where('id_akun', $akun->id_akun)
                ->whereDate('tanggal_pencatatan', '>=', $wStartStr)
                ->whereDate('tanggal_pencatatan', '<=', $wEndStr)
                ->orderBy('tanggal_pencatatan')
                ->orderBy('id_log_berat')
                ->get();

            $wAwal = $prevW
                ? (float) $prevW->berat_badan
                : ($logsW->isNotEmpty() ? (float) $logsW->first()->berat_badan : null);

            $wAkhir = $logsW->isNotEmpty()
                ? (float) $logsW->last()->berat_badan
                : $wAwal;

            $wDiffKg = ($wAwal !== null && $wAkhir !== null && $logsW->isNotEmpty())
                ? round($wAkhir - $wAwal, 3)
                : 0.0;
            $wDiffGram = (int) round($wDiffKg * 1000);
            $wArah = $wDiffGram < 0 ? 'turun' : ($wDiffGram > 0 ? 'naik' : 'tetap');

            $riwayatMingguan[] = [
                'week_offset'    => $w,
                'label_periode'  => $wStart->translatedFormat('d M') . ' - ' . $wEnd->translatedFormat('d M Y'),
                'is_current'     => $w === 0,
                'berat_awal'     => $wAwal,
                'berat_akhir'    => $wAkhir,
                'perubahan_kg'   => $wDiffKg,
                'perubahan_gram' => $wDiffGram,
                'arah'           => $wArah,
                'hari_ditimbang' => $logsW->unique(fn ($r) => Carbon::parse($r->tanggal_pencatatan)->toDateString())->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'week_offset'           => $weekOffset,
                'periode_label'         => $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y'),
                'tanggal_mulai'         => $startDateStr,
                'tanggal_selesai'       => $endDateStr,
                'is_current_week'       => $weekOffset === 0,
                'has_any_history'       => $totalSemuaCatatan > 0,
                'jumlah_hari_ditimbang' => $jumlahHariDitimbang,
                'berat_awal_minggu'     => $beratAwalMinggu,
                'berat_terakhir_minggu' => $beratTerakhirMinggu,
                'total_perubahan_kg'    => $totalPerubahanKg,
                'total_perubahan_gram'  => $totalPerubahanGram,
                'arah_mingguan'         => $arahMingguan,
                'hari_list'             => $hariList,
                'aktivitas_mingguan'    => [
                    'total_langkah'      => $totalLangkahMinggu,
                    'total_jarak_km'     => round($totalJarakMinggu, 2),
                    'total_kalori'       => round($totalKaloriMinggu, 1),
                    'total_durasi_menit' => (int) round($totalDurasiDetikMinggu / 60),
                    'hari_aktif'         => $hariAktifOlahraga,
                ],
                'riwayat_mingguan'      => $riwayatMingguan,
            ],
        ]);
    }

    /**
     * 1C. CATAT / PERBARUI BERAT BADAN HARIAN (ANTI DUPLIKAT TANGGAL SAMA)
     */
    public function storeBeratBadan(Request $request)
    {
        $akun = $request->user();
        $tz = 'Asia/Jakarta';
        $todayLocal = Carbon::now($tz)->toDateString();

        $validated = $request->validate([
            'berat_badan'        => ['required', 'numeric', 'min:30', 'max:250'],
            'tanggal_pencatatan' => ['nullable', 'date', 'before_or_equal:' . $todayLocal],
        ]);

        $berat = round((float) $validated['berat_badan'], 2);
        $tanggalStr = !empty($validated['tanggal_pencatatan'])
            ? Carbon::parse($validated['tanggal_pencatatan'])->toDateString()
            : $todayLocal;

        $tinggi = (float) ($akun->tinggi_badan ?: 170);

        $record = $this->upsertBeratHarian($akun, $berat, $tinggi, $tanggalStr);

        if (empty($akun->berat_badan_awal) || (float) $akun->berat_badan_awal <= 0) {
            $akun->update(['berat_badan_awal' => $berat]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berat badan tanggal ' . Carbon::parse($tanggalStr)->translatedFormat('d M Y') . ' berhasil disimpan!',
            'data'    => $record,
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

        $todayStr = Carbon::now('Asia/Jakarta')->toDateString();
        $this->upsertBeratHarian($akun, $berat, $tinggi, $todayStr);

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
                'id_akun'        => $freshAkun->id_akun,
                'nama_lengkap'   => $freshAkun->nama_lengkap,
                'email'          => $freshAkun->email,
                'jenis_kelamin'  => $freshAkun->jenis_kelamin,
                'usia'           => $freshAkun->usia,
                'tinggi_badan'   => (float) $freshAkun->tinggi_badan,
                'berat_sekarang' => $berat,
                'foto_profil'    => $this->resolveFotoUrl($freshAkun->foto_profil),
                'target_aktif'   => $freshAkun->targetAktif,
            ],
        ]);
    }

    /**
     * 5. JADWAL OLAHRAGA (GET, POST, TOGGLE SELESAI, DELETE)
     */
    public function jadwal(Request $request)
    {
        $jadwal = JadwalOlahraga::with('jenisOlahraga')
            ->where('id_akun', $request->user()->id_akun)
            ->orderBy('tanggal_tugas')
            ->get()
            ->map(function ($item) {
                return [
                    'id_jadwal'      => $item->id_jadwal,
                    'tugas'          => $item->tugas,
                    'jenis_olahraga' => $item->jenisOlahraga->nama_jenis ?? 'Umum',
                    'sumber'         => $item->sumber ?? 'user',
                    'tanggal_tugas'  => $item->tanggal_tugas,
                    'status'         => $item->status ?? 'belum',
                    'selesai'        => strtolower((string) $item->status) === 'selesai',
                ];
            });

        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    public function storeJadwal(Request $request)
    {
        $akun = $request->user();

        $validated = $request->validate([
            'tugas'          => ['required', 'string', 'max:255'],
            'jenis_olahraga' => ['nullable', 'string', 'max:100'],
            'tanggal_tugas'  => ['required', 'date'],
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

        $jadwal = JadwalOlahraga::create([
            'id_akun'       => $akun->id_akun,
            'id_jenis'      => $jenis->id_jenis,
            'id_admin'      => null,
            'sumber'        => 'user',
            'tugas'         => $validated['tugas'],
            'tanggal_tugas' => $validated['tanggal_tugas'],
            'status'        => 'belum',
        ]);

        Notifikasi::create([
            'id_jadwal'     => $jadwal->id_jadwal,
            'pesan'         => 'Jadwal latihan baru: ' . $jadwal->tugas . ' pada ' . $jadwal->tanggal_tugas,
            'waktu_kirim'   => now(),
            'status_dibaca' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal olahraga berhasil ditambahkan!',
            'data'    => $jadwal->load('jenisOlahraga'),
        ], 201);
    }

    public function toggleJadwal(Request $request, $id)
    {
        $jadwal = JadwalOlahraga::where('id_akun', $request->user()->id_akun)
            ->where('id_jadwal', $id)
            ->firstOrFail();

        $sudahSelesai = strtolower((string) $jadwal->status) === 'selesai';
        $jadwal->status = $sudahSelesai ? 'belum' : 'selesai';
        $jadwal->save();

        return response()->json([
            'success' => true,
            'message' => 'Status jadwal diperbarui.',
            'status'  => $jadwal->status,
            'selesai' => !$sudahSelesai,
        ]);
    }

    public function destroyJadwal(Request $request, $id)
    {
        $jadwal = JadwalOlahraga::where('id_akun', $request->user()->id_akun)
            ->where('id_jadwal', $id)
            ->firstOrFail();

        $jadwal->notifikasi()->delete();
        $jadwal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus.',
        ]);
    }

    /**
     * 6. KONTEN EDUKASI & NOTIFIKASI
     */
    public function edukasi()
    {
        $konten = KontenEdukasi::published()
            ->orderByDesc('tanggal_publish')
            ->orderByDesc('id_konten')
            ->get()
            ->map(function ($item) {
                $thumbUrl = null;
                if (!empty($item->thumbnail)) {
                    $thumbUrl = str_starts_with($item->thumbnail, 'http')
                        ? $item->thumbnail
                        : asset('storage/' . ltrim($item->thumbnail, '/'));
                }

                return [
                    'id_konten'         => $item->id_konten,
                    'tipe_konten'       => $item->tipe_konten ?? 'video',
                    'judul'             => $item->judul,
                    'deskripsi'         => $item->deskripsi,
                    'url_video_youtube' => $item->url_video_youtube,
                    'thumbnail'         => $thumbUrl,
                    'konten_teks'       => $item->konten_teks,
                    'kategori'          => $item->kategori ?? 'Olahraga',
                    'tanggal_publish'   => $item->tanggal_publish?->toDateString(),
                ];
            });

        return response()->json(['success' => true, 'data' => $konten]);
    }

    public function notifikasi(Request $request)
    {
        $akunId = $request->user()->id_akun;
        $notif = Notifikasi::with('jadwal')
            ->whereHas('jadwal', function ($q) use ($akunId) {
                $q->where('id_akun', $akunId);
            })
            ->latest('waktu_kirim')
            ->get()
            ->map(function ($item) {
                return [
                    'id_notifikasi' => $item->id_notifikasi,
                    'id_jadwal'     => $item->id_jadwal,
                    'judul'         => $item->jadwal->tugas ?? 'Pengingat Latihan GERAK',
                    'pesan'         => $item->pesan,
                    'waktu_kirim'   => $item->waktu_kirim?->toIso8601String(),
                    'status_dibaca' => (bool) $item->status_dibaca,
                ];
            });

        return response()->json(['success' => true, 'data' => $notif]);
    }

    public function tandaiNotifikasiDibaca(Request $request)
    {
        $akunId = $request->user()->id_akun;
        Notifikasi::whereHas('jadwal', function ($q) use ($akunId) {
            $q->where('id_akun', $akunId);
        })->update(['status_dibaca' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ]);
    }
}