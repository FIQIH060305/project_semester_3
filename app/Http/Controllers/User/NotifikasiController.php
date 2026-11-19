<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $akun = auth('web')->user();

        // Ambil notifikasi milik pengguna ini saja (lewat relasi jadwal → akun)
        $notifikasiList = Notifikasi::whereHas('jadwal', fn ($q) => $q->where('id_akun', $akun->id_akun))
            ->with('jadwal.jenisOlahraga')
            ->orderByDesc('waktu_kirim')
            ->get();

        return view('user.notifikasi.index', compact('notifikasiList'));
    }

    public function markAsRead($id)
    {
        $akun = auth('web')->user();
        $notifikasi = Notifikasi::with('jadwal')->findOrFail($id);

        // Pastikan notifikasi ini hanya dapat dibaca oleh pemilik jadwal terkait
        abort_if(! $notifikasi->jadwal || (int) $notifikasi->jadwal->id_akun !== (int) $akun->id_akun, 403, 'Anda tidak memiliki hak akses untuk notifikasi ini.');

        $notifikasi->update(['status_dibaca' => true]);

        return redirect()->back()->with('success', 'Notifikasi ditandai dibaca.');
    }
}
