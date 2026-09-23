<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $akun = Auth::guard('web')->user();
        $target = $akun->targetAktif;

        return view('user.profile.show', compact('akun', 'target'));
    }

    public function edit()
    {
        $akun = Auth::guard('web')->user();
        $target = $akun->targetAktif;

        return view('user.profile.edit', compact('akun', 'target'));
    }

    public function update(Request $request)
    {
        $akun = Auth::guard('web')->user();

        $validated = $request->validate([
            'tinggi_badan'   => ['required', 'numeric', 'min:140', 'max:200'],
            'berat_badan'    => ['required', 'numeric', 'min:30', 'max:250'],
            'berat_target'   => ['required', 'numeric', 'min:30', 'max:250', 'lt:berat_badan'],
        ], [
            'tinggi_badan.required' => 'Tinggi badan wajib diisi.',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka, contoh: 170 (bukan huruf atau simbol).',
            'tinggi_badan.min' => 'Tinggi badan minimal 140 cm. Mohon periksa kembali angka yang diketik.',
            'tinggi_badan.max' => 'Tinggi badan maksimal 200 cm. Sepertinya ada angka yang salah ketik atau kelebihan digit.',

            'berat_badan.required' => 'Berat badan wajib diisi.',
            'berat_badan.numeric' => 'Berat badan harus berupa angka, contoh: 80 (bukan huruf atau simbol).',
            'berat_badan.min' => 'Berat badan minimal 80 kg. Mohon periksa kembali angka yang diketik.',
            'berat_badan.max' => 'Berat badan maksimal 200 kg. Sepertinya ada angka yang salah ketik atau kelebihan digit.',

            'berat_target.required' => 'Target berat badan wajib diisi.',
            'berat_target.numeric' => 'Target berat badan harus berupa angka.',
            'berat_target.min' => 'Target berat badan minimal 50 kg.',
            'berat_target.max' => 'Target berat badan maksimal 70 kg.',
            'berat_target.lt' => 'Target berat badan harus lebih kecil dari berat badan sekarang (aplikasi ini fokus untuk penurunan berat badan).',
        ]);

        $akun->update(['tinggi_badan' => $validated['tinggi_badan']]);

        $tinggiMeter = $validated['tinggi_badan'] / 100;
        $bmi = round($validated['berat_badan'] / ($tinggiMeter * $tinggiMeter), 1);

        $akun->riwayatBeratBadan()->create([
            'berat_badan' => $validated['berat_badan'],
            'bmi' => $bmi,
            'tanggal_pencatatan' => today(),
        ]);

        $targetAktif = $akun->targetAktif;

        if ($targetAktif) {
            $targetAktif->update([
                'berat_target' => $validated['berat_target'],
            ]);
        } else {
            Target::create([
                'id_akun' => $akun->id_akun,
                'berat_awal' => $validated['berat_badan'],
                'berat_target' => $validated['berat_target'],
                'tanggal_mulai' => today(),
                'status' => 'aktif',
            ]);
        }

        return redirect()->route('user.profile.show')->with('success', 'Profil berhasil diperbarui.');
    }
}