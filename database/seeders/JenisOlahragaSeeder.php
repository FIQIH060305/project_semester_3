<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisOlahragaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jenis_olahraga')->insert([
            [
                'nama_jenis' => 'Berjalan',
                'deskripsi' => 'Aktivitas berjalan kaki dengan intensitas ringan hingga sedang',
                'estimasi_kalori_per_km' => 50,
            ],
            [
                'nama_jenis' => 'Berlari',
                'deskripsi' => 'Aktivitas berlari dengan intensitas sedang hingga tinggi',
                'estimasi_kalori_per_km' => 80,
            ],
        ]);
    }
}