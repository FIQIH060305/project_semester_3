<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisOlahraga extends Model
{
    protected $table = 'jenis_olahraga';
    protected $primaryKey = 'id_jenis';
    public $timestamps = false;

    protected $fillable = ['nama_jenis', 'deskripsi', 'estimasi_kalori_per_km'];
}