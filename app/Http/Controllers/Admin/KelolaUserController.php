<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;

class KelolaUserController extends Controller
{
    public function index()
    {
        $akunList = Akun::latest()->get();
        return view('admin.users.index', compact('akunList'));
    }

    public function toggleStatus($id)
    {
        $akun = Akun::findOrFail($id);
        $akun->status_akun = $akun->status_akun === 'aktif' ? 'blokir' : 'aktif';
        $akun->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Status {$akun->nama_lengkap} berhasil diubah.");
    }
}