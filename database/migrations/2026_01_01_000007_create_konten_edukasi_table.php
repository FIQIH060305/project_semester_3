<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konten_edukasi', function (Blueprint $table) {
            $table->id('id_konten');
            $table->foreignId('id_admin')->constrained('admin', 'id_admin')->cascadeOnDelete();
            $table->enum('tipe_konten', ['video_olahraga', 'video_resep', 'artikel']);
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->string('url_video_youtube')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('konten_teks')->nullable();
            $table->string('kategori', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konten_edukasi');
    }
};