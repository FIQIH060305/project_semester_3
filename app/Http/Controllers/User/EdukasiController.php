<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KontenEdukasi;

class EdukasiController extends Controller
{
    public function index()
    {
        $kontenPerTipe = KontenEdukasi::latest()->get()->groupBy('tipe_konten');

        return view('user.edukasi.index', compact('kontenPerTipe'));
    }
}