<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\JadwalOlahraga;
use App\Models\JenisOlahraga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelolaOlahragaController extends Controller
{
    public function index()
    {
        $akunList = Akun::where('status_akun', 'aktif')->get();
        $jenisList = JenisOlahraga::all();
        $jadwalTerbaru = JadwalOlahraga::with(['akun', 'jenisOlahraga'])
            ->where('sumber', 'admin')->latest()->take(15)->get();

        return view('admin.olahraga.jadwal', compact('akunList', 'jenisList', 'jadwalTerbaru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_akun' => ['required', 'exists:akun,id_akun'],
            'id_jenis' => ['required', 'exists:jenis_olahraga,id_jenis'],
            'tugas' => ['required', 'string', 'max:255'],
            'tanggal_tugas' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'tanggal_tugas.after_or_equal' => 'Tanggal tugas tidak boleh tanggal yang sudah lewat.',
        ]);

        $validated['id_admin'] = Auth::guard('admin')->id();
        $validated['sumber'] = 'admin';

        $jadwal = JadwalOlahraga::create($validated);

        \App\Models\Notifikasi::create([
            'id_jadwal' => $jadwal->id_jadwal,
            'pesan' => "Admin membagikan jadwal olahraga baru: \"{$jadwal->tugas}\" untuk tanggal " .
                       \Carbon\Carbon::parse($jadwal->tanggal_tugas)->translatedFormat('d M Y'),
            'waktu_kirim' => now(),
            'status_dibaca' => false,
        ]);

        return redirect()->route('admin.olahraga.jadwal')->with('success', 'Jadwal berhasil dibagikan ke pengguna.');
    }

    public function edit($id)
    {
        $jadwal = JadwalOlahraga::findOrFail($id);

        if ($jadwal->status === 'selesai') {
            return redirect()->route('admin.olahraga.jadwal')
                ->with('error', 'Jadwal yang sudah selesai tidak dapat diedit.');
        }

        $akunList = Akun::where('status_akun', 'aktif')->get();
        $jenisList = JenisOlahraga::all();

        return view('admin.olahraga.edit', compact('jadwal', 'akunList', 'jenisList'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalOlahraga::findOrFail($id);

        if ($jadwal->status === 'selesai') {
            return redirect()->route('admin.olahraga.jadwal')
                ->with('error', 'Jadwal yang sudah selesai tidak dapat diedit.');
        }

        $validated = $request->validate([
            'id_akun' => ['required', 'exists:akun,id_akun'],
            'id_jenis' => ['required', 'exists:jenis_olahraga,id_jenis'],
            'tugas' => ['required', 'string', 'max:255'],
            'tanggal_tugas' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'tanggal_tugas.after_or_equal' => 'Tanggal tugas tidak boleh tanggal yang sudah lewat.',
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.olahraga.jadwal')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalOlahraga::findOrFail($id);

        if ($jadwal->status === 'selesai') {
            return redirect()->route('admin.olahraga.jadwal')
                ->with('error', 'Jadwal yang sudah selesai tidak dapat dihapus.');
        }

        $jadwal->delete();

        return redirect()->route('admin.olahraga.jadwal')->with('success', 'Jadwal berhasil dihapus.');
    }
}