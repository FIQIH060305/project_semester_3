<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target', function (Blueprint $table) {
            $table->id('id_target');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->float('berat_awal');
            $table->float('berat_target');
            $table->integer('target_langkah_harian')->nullable();
            $table->float('target_jarak_harian')->nullable();
            $table->float('target_kalori_harian')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_target')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target');
    }
};