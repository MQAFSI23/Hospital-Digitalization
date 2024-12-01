<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ObatController;
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
        // Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        // Daftar Pengguna
        Route::get('/admin/daftar-pengguna', [AdminController::class, 'daftarPengguna'])
            ->name('admin.daftarPengguna');

        Route::get('/admin/detail-pengguna/{id}', [AdminController::class, 'detailPengguna'])
            ->name('admin.detailPengguna');

        Route::get('/admin/edit-pengguna/{id}', [AdminController::class, 'editPengguna'])
            ->name('admin.editPengguna');

        Route::put('/admin/update-pengguna/{id}', [AdminController::class, 'updatePengguna'])
            ->name('admin.updatePengguna');

        Route::delete('/admin/hapus-pengguna/{id}', [AdminController::class, 'hapusPengguna'])
            ->name('admin.hapusPengguna');

        // Register Admin
        Route::get('/register-admin', [RegisteredUserController::class, 'createAdmin'])
            ->name('register-admin');
        
        Route::post('/register-admin', [RegisteredUserController::class, 'storeAdmin'])
            ->name('register-admin.store');

        // Daftar Obat
        Route::get('/admin/daftar-obat', [ObatController::class, 'daftarObat'])
            ->name('admin.daftarObat');

        Route::get('/admin/detail-obat/{id}', [ObatController::class, 'detailObat'])
            ->name('admin.detailObat');

        Route::get('/admin/edit-obat/{id}', [ObatController::class, 'editObat'])
            ->name('admin.editObat');

        Route::put('/admin/update-obat/{id}', [ObatController::class, 'updateObat'])
            ->name('admin.updateObat');

        Route::delete('/admin/hapus-obat/{id}', [ObatController::class, 'hapusObat'])
            ->name('admin.hapusObat');

        Route::get('/admin/register-obat', [ObatController::class, 'registerObat'])
            ->name('admin.registerObat');

        Route::post('/admin/register-obat', [ObatController::class, 'storeObat'])
            ->name('admin.registerObat.store');
            
        Route::get('/admin/log-obat', [ObatController::class, 'logObat'])
            ->name('admin.logObat');
        
        // Riwayat Pemeriksaan
        Route::get('/admin/riwayat-periksa', [AdminController::class, 'riwayatPeriksa'])
            ->name('admin.riwayatPeriksa');

        Route::get('/admin/detail-riwayat-periksa/{id}', [AdminController::class, 'detailRiwayatPeriksa'])
            ->name('admin.detailRiwayatPeriksa');

        // Status Pengambilan Obat
        Route::patch('/admin/riwayat-periksa/{rekamMedis}/update-resep', [AdminController::class, 'updateResepStatus'])
            ->name('admin.updateResepStatus');

    });

    // Dokter Routes
    Route::middleware('role:dokter')->group(function () {
        Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])
            ->name('dokter.dashboard');
            
        Route::get('/dokter/daftar-pasien', [DokterController::class, 'daftarPasien'])
            ->name('dokter.daftarPasien');

        Route::get('/dokter/detail-pasien/{id}', [DokterController::class, 'detailPasien'])
            ->name('dokter.detailPasien');

        Route::get('/dokter/rekam-medis/{id}', [DokterController::class, 'detailRekamMedis'])
            ->name('dokter.detailRekamMedis');

        Route::get('/dokter/selesai/{penjadwalan}', [DokterController::class, 'selesaiKonsultasi'])
            ->name('dokter.selesaiKonsultasi');
        
        Route::post('/dokter/selesai/{penjadwalan}', [DokterController::class, 'selesaiStore'])
            ->name('dokter.selesaiStore');
        
        Route::delete('/rekam-medis/{rekamMedis}', [DokterController::class, 'destroy'])
            ->name('rekamMedis.destroy');
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