<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_olahraga', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->foreignId('id_jenis')->constrained('jenis_olahraga', 'id_jenis');
            $table->foreignId('id_admin')->nullable()->constrained('admin', 'id_admin')->nullOnDelete();
            $table->enum('sumber', ['user', 'admin'])->default('user');
            $table->string('tugas');
            $table->date('tanggal_tugas');
            $table->enum('status', ['belum', 'selesai'])->default('belum');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_olahraga');
    }
};