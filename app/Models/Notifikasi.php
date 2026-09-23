<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = ['id_jadwal', 'pesan', 'waktu_kirim', 'status_dibaca'];

    public function jadwal()
    {
        return $this->belongsTo(JadwalOlahraga::class, 'id_jadwal', 'id_jadwal');
    }
}