<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\EdukasiController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RekapController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\KelolaUserController;
use App\Http\Controllers\Admin\KelolaKontenController;
use App\Http\Controllers\Admin\KelolaOlahragaController;
use App\Http\Controllers\Admin\RekapitulasiController;
use App\Http\Controllers\User\NotifikasiController;

Route::get('/', fn () => redirect()->route('login'));

// --- Auth Pengguna ---
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// --- Auth Register ---
Route::get('/daftar', [LoginController::class, 'showRegister'])->name('register');                       
Route::post('/daftar', [LoginController::class, 'register'])->name('register.submit');                    
Route::get('/lengkapi-profil', [LoginController::class, 'showCompleteProfile'])->name('complete-profile'); 
Route::post('/lengkapi-profil', [LoginController::class, 'completeProfile'])->name('complete-profile.submit'); 

// --- Auth Admin ---
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// --- Web Pengguna (dikunci, wajib login) ---
Route::prefix('user')->name('user.')->middleware('is.user')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi.index');
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit'); 
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
});

// --- Web Admin (dikunci, wajib login, guard khusus admin) ---
Route::prefix('admin')->name('admin.')->middleware('is.admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [KelolaUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/toggle-status', [KelolaUserController::class, 'toggleStatus'])->name('users.toggle');

    Route::get('/konten', [KelolaKontenController::class, 'index'])->name('konten.index');
    Route::post('/konten', [KelolaKontenController::class, 'store'])->name('konten.store');
    Route::get('/konten/{id}/edit', [KelolaKontenController::class, 'edit'])->name('konten.edit'); 
    Route::put('/konten/{id}', [KelolaKontenController::class, 'update'])->name('konten.update');         
    Route::post('/konten/{id}/hapus', [KelolaKontenController::class, 'destroy'])->name('konten.destroy');
    
    Route::get('/olahraga/jadwal', [KelolaOlahragaController::class, 'index'])->name('olahraga.jadwal');
    Route::post('/olahraga/jadwal', [KelolaOlahragaController::class, 'store'])->name('olahraga.jadwal.store');
    Route::get('/olahraga/jadwal/{id}/edit', [KelolaOlahragaController::class, 'edit'])->name('olahraga.jadwal.edit');           
Route::put('/olahraga/jadwal/{id}', [KelolaOlahragaController::class, 'update'])->name('olahraga.jadwal.update');         
Route::post('/olahraga/jadwal/{id}/hapus', [KelolaOlahragaController::class, 'destroy'])->name('olahraga.jadwal.destroy');   

    Route::get('/rekapan', [RekapitulasiController::class, 'index'])->name('rekapan.index');
});