<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\Admin;
use App\Models\JadwalOlahraga;
use App\Models\Notifikasi;
use App\Models\RiwayatBeratBadan;
use App\Models\RiwayatOlahraga;
use App\Models\Target;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $namaDepan = ['Budi', 'Siti', 'Rudi', 'Ani', 'Joko', 'Dewi', 'Andi', 'Rina', 'Agus', 'Wati',
                      'Eko', 'Nur', 'Hendra', 'Yuli', 'Dedi', 'Fitri', 'Bayu', 'Indah', 'Fajar', 'Lina'];
        $namaBelakang = ['Setiawan', 'Wijaya', 'Kusuma', 'Pratama', 'Saputra', 'Handayani', 'Nugroho',
                         'Lestari', 'Rahman', 'Susanti'];

        $admin = Admin::first();

        for ($i = 0; $i < 20; $i++) {
            $nama = $namaDepan[$i % count($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $jenisKelamin = rand(0, 1) ? 'L' : 'P';
            $usia = rand(18, 55);
            $tinggi = $jenisKelamin === 'L' ? rand(160, 178) : rand(150, 168);
            $beratAwal = rand(65, 110);
            $beratTarget = $beratAwal - rand(8, 25);
            $statusAkun = rand(1, 10) === 1 ? 'blokir' : 'aktif'; // 1 dari 10 sengaja dibuat blokir untuk testing

            $akun = Akun::create([
                'nama_lengkap' => $nama,
                'email' => strtolower(str_replace(' ', '.', $nama)) . rand(10, 999) . '@test.com',
                'password' => Hash::make('password123'),
                'jenis_kelamin' => $jenisKelamin,
                'usia' => $usia,
                'tinggi_badan' => $tinggi,
                'berat_badan_awal' => $beratAwal,
                'status_akun' => $statusAkun,
            ]);

            // Target aktif
            Target::create([
                'id_akun' => $akun->id_akun,
                'berat_awal' => $beratAwal,
                'berat_target' => $beratTarget,
                'target_langkah_harian' => rand(5000, 10000),
                'target_jarak_harian' => rand(3, 8),
                'target_kalori_harian' => rand(300, 600),
                'tanggal_mulai' => now()->subDays(rand(30, 90)),
                'status' => 'aktif',
            ]);

            // Riwayat berat badan (tren menurun bertahap, 4-8 entri selama 2 bulan terakhir)
            $beratSekarang = $beratAwal;
            $jumlahLog = rand(4, 8);
            for ($j = $jumlahLog; $j > 0; $j--) {
                $beratSekarang -= rand(0, 2); // sedikit demi sedikit turun
                $tinggiMeter = $tinggi / 100;
                $bmi = round($beratSekarang / ($tinggiMeter * $tinggiMeter), 1);

                RiwayatBeratBadan::create([
                    'id_akun' => $akun->id_akun,
                    'berat_badan' => $beratSekarang,
                    'bmi' => $bmi,
                    'tanggal_pencatatan' => now()->subDays($j * 7),
                ]);
            }

            // Riwayat olahraga (5-15 sesi tersebar 2 bulan terakhir)
            $jumlahSesi = rand(5, 15);
            for ($k = 0; $k < $jumlahSesi; $k++) {
                $jarak = round(rand(20, 80) / 10, 1); // 2.0 - 8.0 km
                $idJenis = rand(1, 2);
                $kaloriPerKm = $idJenis === 1 ? 50 : 80;

                RiwayatOlahraga::create([
                    'id_akun' => $akun->id_akun,
                    'id_jenis' => $idJenis,
                    'jarak_km' => $jarak,
                    'durasi_waktu' => sprintf('00:%02d:00', rand(15, 55)),
                    'jumlah_langkah' => (int) ($jarak * 1300),
                    'kalori_terbakar' => round($jarak * $kaloriPerKm),
                    'tanggal_waktu' => now()->subDays(rand(0, 60))->subHours(rand(0, 23)),
                ]);
            }

            // Sebagian akun (1 dari 4) dapat jadwal kiriman dari admin + notifikasi
            if ($admin && $i % 4 === 0) {
                $jadwal = JadwalOlahraga::create([
                    'id_akun' => $akun->id_akun,
                    'id_jenis' => rand(1, 2),
                    'id_admin' => $admin->id_admin,
                    'sumber' => 'admin',
                    'tugas' => 'Jalan pagi 30 menit rutin',
                    'tanggal_tugas' => now()->addDays(rand(1, 5)),
                    'status' => 'belum',
                ]);

                Notifikasi::create([
                    'id_jadwal' => $jadwal->id_jadwal,
                    'pesan' => "Admin membagikan jadwal olahraga baru: \"{$jadwal->tugas}\"",
                    'waktu_kirim' => now(),
                    'status_dibaca' => false,
                ]);
            }
        }

        $this->command->info('20 akun dummy + riwayat + target berhasil dibuat.');
    }
}