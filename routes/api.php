<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Endpoint Publik Autentikasi Mobile
Route::post('/mobile/login', [MobileAuthController::class, 'login']);
Route::post('/mobile/register', [MobileAuthController::class, 'register']);
Route::post('/mobile/forgot-password', [MobileAuthController::class, 'sendResetOtp']);
Route::post('/mobile/reset-password', [MobileAuthController::class, 'resetPasswordWithOtp']);

// Endpoint Terproteksi Token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mobile/profile', function (Request $request) {
        return response()->json([
            'success' => true,
            'user'    => $request->user()->load('targetAktif'),
        ]);
    });

    Route::get('/mobile/dashboard', [MobileDataController::class, 'dashboard']);
    Route::get('/mobile/aktivitas', [MobileDataController::class, 'indexAktivitas']);
    Route::post('/mobile/aktivitas', [MobileDataController::class, 'storeAktivitas']);
    Route::post('/mobile/profile/update', [MobileDataController::class, 'updateProfile']);

    // Pemantauan Berat Badan Mingguan (Senin - Minggu)
    Route::get('/mobile/berat-badan/mingguan', [MobileDataController::class, 'beratBadanMingguan']);
    Route::post('/mobile/berat-badan/catat', [MobileDataController::class, 'storeBeratBadan']);

    // Jadwal Olahraga (CRUD + Toggle Selesai)
    Route::get('/mobile/jadwal', [MobileDataController::class, 'jadwal']);
    Route::post('/mobile/jadwal', [MobileDataController::class, 'storeJadwal']);
    Route::patch('/mobile/jadwal/{id}/toggle', [MobileDataController::class, 'toggleJadwal']);
    Route::delete('/mobile/jadwal/{id}', [MobileDataController::class, 'destroyJadwal']);

    // Edukasi & Notifikasi
    Route::get('/mobile/edukasi', [MobileDataController::class, 'edukasi']);
    Route::get('/mobile/notifikasi', [MobileDataController::class, 'notifikasi']);
    Route::post('/mobile/notifikasi/baca', [MobileDataController::class, 'tandaiNotifikasiDibaca']);
});