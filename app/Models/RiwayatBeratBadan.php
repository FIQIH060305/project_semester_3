<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatBeratBadan extends Model
{
    protected $table = 'riwayat_berat_badan';
    protected $primaryKey = 'id_log_berat';
    public $timestamps = false;

    protected $fillable = ['id_akun', 'berat_badan', 'bmi', 'tanggal_pencatatan'];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }
}