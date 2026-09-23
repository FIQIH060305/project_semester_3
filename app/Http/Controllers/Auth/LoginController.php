<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::guard('web')->user()->status_akun !== 'aktif') {
                Auth::guard('web')->logout();
                return back()->withErrors(['email' => 'Akun kamu sedang diblokir. Silakan hubungi admin.']);
            }

            return redirect()->route('user.dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah. Silakan periksa kembali.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

    $validated = $request->validate([
        'nama_lengkap' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
        'email' => ['required', 'email:rfc,dns', 'max:100', 'unique:akun,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ], [
        'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
        'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
        'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi, tidak boleh ada angka atau simbol.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid, contoh: nama@gmail.com. Pastikan nama domainnya benar (gmail.com, bukan gmail.com123).',
        'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun kamu.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok dengan password di atas.',
    ]);

    session([
        'registrasi_data' => [
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ],
    ]);

    return redirect()->route('complete-profile');
    }

    public function showCompleteProfile()
    {
        if (! session()->has('registrasi_data')) {
            return redirect()->route('register')
                ->withErrors(['nama_lengkap' => 'Silakan isi data akun terlebih dahulu.']);
        }

        return view('auth.complete-profile');
    }

    public function completeProfile(Request $request)
    {
        if (! session()->has('registrasi_data')) {
            return redirect()->route('register')
                ->withErrors(['nama_lengkap' => 'Sesi registrasi sudah berakhir. Silakan isi ulang data akun.']);
        }

        $validated = $request->validate([
            'usia' => ['required', 'integer', 'min:15', 'max:65'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tinggi_badan' => ['required', 'numeric', 'min:140', 'max:200'],
            'berat_badan' => ['required', 'numeric', 'min:80', 'max:200'],
            'target_berat' => ['required', 'numeric', 'min:50', 'max:70', 'lt:berat_badan'],
        ], [
            'usia.required' => 'Usia wajib diisi.',
            'usia.integer' => 'Usia harus berupa angka bulat, contoh: 25 (bukan huruf atau desimal).',
            'usia.min' => 'Usia minimal 15 tahun. Aplikasi ini ditujukan untuk remaja akhir dan dewasa.',
            'usia.max' => 'Usia maksimal 65 tahun, sesuai rentang usia dewasa produktif menurut Kemenkes RI.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'tinggi_badan.required' => 'Tinggi badan wajib diisi.',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka, contoh: 170 (bukan huruf).',
            'tinggi_badan.min' => 'Tinggi badan minimal 140 cm. Mohon periksa kembali data yang diinput.',
            'tinggi_badan.max' => 'Tinggi badan maksimal 200 cm. Mohon periksa kembali data yang diinput.',
            'berat_badan.required' => 'Berat badan wajib diisi.',
            'berat_badan.numeric' => 'Berat badan harus berupa angka, contoh: 70 (bukan huruf).',
            'berat_badan.min' => 'Berat badan minimal 80 kg. Mohon periksa kembali data yang diinput.',
            'berat_badan.max' => 'Berat badan maksimal 200 kg. Mohon periksa kembali data yang diinput.',
            'target_berat.required' => 'Target berat badan wajib diisi.',
            'target_berat.numeric' => 'Target berat badan harus berupa angka.',
            'target_berat.min' => 'Target berat badan minimal 50 kg.',
            'target_berat.max' => 'Target berat badan maksimal 70 kg.',
            'target_berat.lt' => 'Target berat badan harus lebih kecil dari berat badan sekarang (aplikasi ini fokus untuk penurunan berat badan).',
        ]);

        $dataAkun = session('registrasi_data');

        $akun = Akun::create([
            'nama_lengkap' => $dataAkun['nama_lengkap'],
            'email' => $dataAkun['email'],
            'password' => $dataAkun['password'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'usia' => $validated['usia'],
            'tinggi_badan' => $validated['tinggi_badan'],
            'berat_badan_awal' => $validated['berat_badan'],
            'status_akun' => 'aktif',
        ]);

        Target::create([
            'id_akun' => $akun->id_akun,
            'berat_awal' => $validated['berat_badan'],
            'berat_target' => $validated['target_berat'],
            'tanggal_mulai' => today(),
            'status' => 'aktif',
        ]);

        session()->forget('registrasi_data');

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat! Silakan masuk menggunakan email dan password kamu.');
    }
}