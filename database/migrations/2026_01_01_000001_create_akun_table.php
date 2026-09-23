<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->id('id_akun');
            $table->string('nama_lengkap', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->unsignedTinyInteger('usia');
            $table->float('tinggi_badan');
            $table->float('berat_badan_awal');
            $table->string('foto_profil')->nullable();
            $table->enum('status_akun', ['aktif', 'blokir'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};