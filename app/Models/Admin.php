<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    protected $fillable = ['nama', 'email', 'password'];

    protected $hidden = ['password'];

    public function kontenEdukasi()
    {
        return $this->hasMany(KontenEdukasi::class, 'id_admin', 'id_admin');
    }

    public function jadwalOlahraga()
    {
        return $this->hasMany(JadwalOlahraga::class, 'id_admin', 'id_admin');
    }
}