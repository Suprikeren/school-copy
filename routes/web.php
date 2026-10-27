<?php

use App\Http\Controllers\Dashboard\JenisPelanggaranController;
use App\Http\Controllers\Dashboard\PasalTatibController;
use App\Http\Controllers\Dashboard\PelanggaranController;
use App\Http\Controllers\Dashboard\PengajuanPerubahanController;
use App\Http\Controllers\Dashboard\RiwayatKelasController;
use App\Http\Controllers\Dashboard\SiswaController;
use App\Http\Controllers\Dashboard\Spatie\PermissionController;
use App\Http\Controllers\Dashboard\Spatie\RoleController;
use App\Http\Controllers\Dashboard\Spatie\UserController;
use App\Http\Controllers\Dashboard\TindakLanjutController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);

        Route::prefix('tatib')->name('tatib.')->group(function () {
            Route::resource('pasal', PasalTatibController::class);
            Route::resource('jenis-pelanggaran', JenisPelanggaranController::class);
        });

        Route::resource('siswa', SiswaController::class);

        Route::resource('riwayat-kelas', RiwayatKelasController::class)
            ->parameters(['riwayat-kelas' => 'riwayat_kelas'])
            ->except(['show']);

        Route::resource('pelanggaran', PelanggaranController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::resource('tindak-lanjut', TindakLanjutController::class)
            ->except(['show']);

        Route::resource('pengajuan', PengajuanPerubahanController::class)
            ->only(['index', 'store', 'show']);
        Route::put('pengajuan/{pengajuan}/putuskan', [PengajuanPerubahanController::class, 'putuskan'])
            ->name('pengajuan.putuskan');
    });
});

require __DIR__.'/settings.php';
