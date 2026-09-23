<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Akun extends Authenticatable
{
    protected $table = 'akun';
    protected $primaryKey = 'id_akun';

    protected $fillable = [
        'nama_lengkap', 'email', 'password', 'jenis_kelamin',
        'usia', 'tinggi_badan', 'berat_badan_awal', 'foto_profil', 'status_akun',
    ];

    protected $hidden = ['password'];

    public function target()
    {
        return $this->hasMany(Target::class, 'id_akun', 'id_akun');
    }

    public function targetAktif()
    {
        return $this->hasOne(Target::class, 'id_akun', 'id_akun')
            ->where('status', 'aktif')->latestOfMany('tanggal_mulai');
    }

    public function riwayatBeratBadan()
    {
        return $this->hasMany(RiwayatBeratBadan::class, 'id_akun', 'id_akun');
    }

    public function riwayatOlahraga()
    {
        return $this->hasMany(RiwayatOlahraga::class, 'id_akun', 'id_akun');
    }

    public function jadwalOlahraga()
    {
        return $this->hasMany(JadwalOlahraga::class, 'id_akun', 'id_akun');
    }
}