<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
    if (Auth::check()) {
        $userRole = Auth::user()->role;

        return match ($userRole) {
            'admin' => redirect()->route('admin.dashboard'),
            'dokter' => redirect()->route('dokter.dashboard'),
            'pasien' => redirect()->route('pasien.dashboard'),
            default => redirect('/'),
        };
    }

    return view('auth.login2');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/admin/daftar-pengguna', [AdminController::class, 'daftarPengguna'])
            ->name('admin.daftarPengguna');

        Route::get('/admin/edit-pengguna/{id}', [AdminController::class, 'editPengguna'])
            ->name('admin.editPengguna');

        Route::delete('/admin/hapus-pengguna/{id}', [AdminController::class, 'hapusPengguna'])
            ->name('admin.hapusPengguna');

        Route::get('/register-admin', [RegisteredUserController::class, 'createAdmin'])
            ->name('register-admin');

        Route::post('/register-admin', [RegisteredUserController::class, 'storeAdmin'])
            ->name('register-admin.store');
    });

    // Dokter Routes
    Route::middleware('role:dokter')->group(function () {
        Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])
            ->name('dokter.dashboard');
    });

    // Pasien Routes
    Route::middleware('role:pasien')->group(function () {
        Route::middleware('role:pasien')->get('/pasien/dashboard', [PasienController::class, 'dashboard'])
            ->name('pasien.dashboard');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';