<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatOlahraga extends Model
{
    protected $table = 'riwayat_olahraga';
    protected $primaryKey = 'id_riwayat';
    public $timestamps = false;

    protected $fillable = [
        'id_akun', 'id_jenis', 'id_jadwal',
        'jarak_km', 'durasi_waktu', 'jumlah_langkah', 'kalori_terbakar', 'tanggal_waktu',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }

    public function jenisOlahraga()
    {
        return $this->belongsTo(JenisOlahraga::class, 'id_jenis', 'id_jenis');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalOlahraga::class, 'id_jadwal', 'id_jadwal');
    }
}