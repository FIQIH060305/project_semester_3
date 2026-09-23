<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_bulanan', function (Blueprint $table) {
            $table->id('id_rekap');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->float('total_jarak_km')->default(0);
            $table->float('total_kalori_terbakar')->default(0);
            $table->integer('total_langkah')->default(0);
            $table->float('rata_rata_berat_badan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_bulanan');
    }
};