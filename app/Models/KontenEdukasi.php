<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontenEdukasi extends Model
{
    protected $table = 'konten_edukasi';
    protected $primaryKey = 'id_konten';

    protected $fillable = [
        'id_admin', 'tipe_konten', 'judul', 'deskripsi',
        'url_video_youtube', 'thumbnail', 'konten_teks', 'kategori',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}