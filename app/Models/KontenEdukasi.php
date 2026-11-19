<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KontenEdukasi extends Model
{
    protected $table = 'konten_edukasi';

    protected $primaryKey = 'id_konten';

    protected $fillable = [
        'id_admin', 'tipe_konten', 'judul', 'deskripsi',
        'url_video_youtube', 'thumbnail', 'konten_teks', 'kategori', 'tanggal_publish',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publish' => 'date',
        ];
    }

    /**
     * Scope untuk konten yang sudah mencapai tanggal publish.
     * Konten dengan tanggal publish di masa depan tidak ditampilkan ke pengguna.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('tanggal_publish')
                ->orWhereDate('tanggal_publish', '<=', today());
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
