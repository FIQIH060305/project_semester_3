<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_olahraga', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->foreignId('id_jenis')->constrained('jenis_olahraga', 'id_jenis');
            $table->foreignId('id_jadwal')->nullable()->constrained('jadwal_olahraga', 'id_jadwal')->nullOnDelete();
            $table->float('jarak_km');
            $table->time('durasi_waktu');
            $table->integer('jumlah_langkah');
            $table->float('kalori_terbakar');
            $table->dateTime('tanggal_waktu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_olahraga');
    }
};