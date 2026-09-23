<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_berat_badan', function (Blueprint $table) {
            $table->id('id_log_berat');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->float('berat_badan');
            $table->float('bmi');
            $table->date('tanggal_pencatatan');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_berat_badan');
    }
};