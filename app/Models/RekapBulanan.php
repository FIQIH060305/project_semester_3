<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapBulanan extends Model
{
    protected $table = 'rekap_bulanan';
    protected $primaryKey = 'id_rekap';
    public $timestamps = false;

    protected $fillable = [
        'id_akun', 'bulan', 'tahun',
        'total_jarak_km', 'total_kalori_terbakar', 'total_langkah', 'rata_rata_berat_badan',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }
}