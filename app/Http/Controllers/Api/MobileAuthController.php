<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Target;
use App\Support\Antropometri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MobileAuthController extends Controller
{
    /**
     * 1. LOGIN MOBILE (Satu akun dengan Web)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $akun = Akun::where('email', $request->email)->first();

        if (!$akun || !Hash::check($request->password, $akun->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah. Silakan periksa kembali.',
            ], 401);
        }

        if ($akun->status_akun !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun kamu sedang diblokir. Silakan hubungi admin.',
            ], 403);
        }

        $token = $akun->createToken('gerak-mobile-app')->plainTextToken;

        $fotoUrl = null;
        if (!empty($akun->foto_profil)) {
            $fotoUrl = str_starts_with($akun->foto_profil, 'http')
                ? $akun->foto_profil
                : asset('storage/' . ltrim($akun->foto_profil, '/'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id_akun'          => $akun->id_akun,
                'nama_lengkap'     => $akun->nama_lengkap,
                'email'            => $akun->email,
                'jenis_kelamin'    => $akun->jenis_kelamin,
                'usia'             => $akun->usia,
                'tinggi_badan'     => $akun->tinggi_badan,
                'berat_badan_awal' => $akun->berat_badan_awal,
                'foto_profil'      => $fotoUrl,
                'target_aktif'     => $akun->targetAktif,
            ],
        ]);
    }

    /**
     * 2. REGISTRASI LENGKAP DARI MOBILE (Akun + Lengkapi Profil sekaligus)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'  => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'         => ['required', 'email', 'max:100', 'unique:akun,email'],
            'password'      => ['required', 'string', 'min:8'],
            'usia'          => ['required', 'integer', 'min:19', 'max:65'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tinggi_badan'  => ['required', 'numeric', 'min:140', 'max:200'],
            'berat_badan'   => ['required', 'numeric', 'min:30', 'max:250'],
            'target_berat'  => ['required', 'numeric', 'lt:berat_badan'],
        ]);

        try {
            Antropometri::pastikanTargetWajar(
                (float) $validated['tinggi_badan'],
                (float) $validated['berat_badan'],
                (float) $validated['target_berat'],
                'target_berat'
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $akun = Akun::create([
            'nama_lengkap'     => $validated['nama_lengkap'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'jenis_kelamin'    => $validated['jenis_kelamin'],
            'usia'             => $validated['usia'],
            'tinggi_badan'     => $validated['tinggi_badan'],
            'berat_badan_awal' => $validated['berat_badan'],
            'status_akun'      => 'aktif',
        ]);

        Target::create([
            'id_akun'       => $akun->id_akun,
            'berat_awal'    => $validated['berat_badan'],
            'berat_target'  => $validated['target_berat'],
            'tanggal_mulai' => today(),
            'status'        => 'aktif',
        ]);

        $token = $akun->createToken('gerak-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dibuat!',
            'token'   => $token,
            'user'    => $akun->load('targetAktif'),
        ], 201);
    }

    /**
     * 3. KIRIM KODE OTP RESET PASSWORD KE EMAIL
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $akun = Akun::where('email', $request->email)->first();

        if (!$akun) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar pada sistem GERAK.',
            ], 404);
        }

        if ($akun->status_akun !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun kamu sedang diblokir. Tidak dapat mereset password.',
            ], 403);
        }

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        try {
            Mail::raw(
                "Halo {$akun->nama_lengkap},\n\n" .
                "Kami menerima permintaan untuk mengatur ulang kata sandi akun GERAK kamu.\n" .
                "Gunakan kode verifikasi (OTP) berikut di aplikasi:\n\n" .
                "KODE OTP: {$otp}\n\n" .
                "Kode ini berlaku selama 15 menit. Demi keamanan, jangan bagikan kode ini kepada siapa pun.\n" .
                "Jika kamu tidak merasa meminta reset password, abaikan email ini.\n\n" .
                "Salam sehat,\nTim GERAK",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Kode Verifikasi Reset Password - GERAK');
                }
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email verifikasi. Periksa konfigurasi SMTP server.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi 6 digit telah dikirim ke email ' . $request->email,
        ]);
    }

    /**
     * 4. VERIFIKASI OTP & SIMPAN PASSWORD BARU
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email'              => ['required', 'email'],
            'otp'                => ['required', 'digits:6'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'otp.required'       => 'Kode verifikasi wajib diisi.',
            'otp.digits'         => 'Kode verifikasi harus 6 digit angka.',
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->otp, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi salah atau tidak valid.',
            ], 400);
        }

        if (now()->diffInMinutes($record->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi sudah kedaluwarsa (lebih dari 15 menit). Silakan minta kode baru.',
            ], 400);
        }

        Akun::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $akun = Akun::where('email', $request->email)->first();
        if ($akun) {
            $akun->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui! Silakan masuk menggunakan password barumu.',
        ]);
    }
}