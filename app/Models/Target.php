<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table = 'target';
    protected $primaryKey = 'id_target';
    public $timestamps = false;

    protected $fillable = [
        'id_akun', 'berat_awal', 'berat_target',
        'target_langkah_harian', 'target_jarak_harian', 'target_kalori_harian',
        'tanggal_mulai', 'tanggal_target', 'status',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }
}