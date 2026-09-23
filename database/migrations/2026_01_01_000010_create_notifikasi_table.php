<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_jadwal')->constrained('jadwal_olahraga', 'id_jadwal')->cascadeOnDelete();
            $table->string('pesan');
            $table->dateTime('waktu_kirim');
            $table->boolean('status_dibaca')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};