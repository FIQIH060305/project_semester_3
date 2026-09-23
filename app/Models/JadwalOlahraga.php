<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalOlahraga extends Model
{
    protected $table = 'jadwal_olahraga';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;

    protected $fillable = [
        'id_akun', 'id_jenis', 'id_admin', 'sumber', 'tugas', 'tanggal_tugas', 'status',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }

    public function jenisOlahraga()
    {
        return $this->belongsTo(JenisOlahraga::class, 'id_jenis', 'id_jenis');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_jadwal', 'id_jadwal');
    }
}