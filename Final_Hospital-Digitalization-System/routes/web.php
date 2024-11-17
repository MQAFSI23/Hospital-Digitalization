<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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
        Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('register-admin', [RegisteredUserController::class, 'createAdmin'])->name('register-admin');
        Route::post('register-admin', [RegisteredUserController::class, 'storeAdmin'])->name('register-admin.store');
    });

    // Dokter Routes
    Route::middleware('role:dokter')->get('/dokter/dashboard', [HomeController::class, 'dokterDashboard'])->name('dokter.dashboard');

    // Pasien Routes
    Route::middleware('role:pasien')->get('/pasien/dashboard', [HomeController::class, 'pasienDashboard'])->name('pasien.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';