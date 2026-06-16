<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master;
use App\Http\Controllers\ReklameController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::get('/report/cetak', [ReportController::class, 'cetak'])->name('report.cetak');

    // ── Data SPK ──────────────────────────────────────────────────────────
    Route::resource('spk', SpkController::class);
    Route::delete('spk/{spk}/toko/{reklame}', [SpkController::class, 'destroyToko'])->name('spk.toko.destroy');

    // ── Data Reklame (item per toko — edit/show) ──────────────────────────
    Route::prefix('reklame')->name('reklame.')->group(function () {
        Route::get('/', [ReklameController::class, 'index'])->name('index');

        Route::middleware('role:superadmin,staff')->group(function () {
            Route::get('/create', [ReklameController::class, 'create'])->name('create');
            Route::post('/', [ReklameController::class, 'store'])->name('store');
        });

        Route::get('/{reklame}', [ReklameController::class, 'show'])->name('show');
        Route::get('/{reklame}/edit', [ReklameController::class, 'edit'])->name('edit');
        Route::put('/{reklame}', [ReklameController::class, 'update'])->name('update');
        Route::middleware('role:superadmin')->delete('/{reklame}', [ReklameController::class, 'destroy'])->name('destroy');
    });

    // ── User Management (superadmin only) ────────────────────────────────
    Route::middleware('role:superadmin')->prefix('users')->name('users.')->group(function () {
        Route::get('/',             [UserController::class, 'index'])->name('index');
        Route::post('/',            [UserController::class, 'store'])->name('store');
        Route::put('/{user}',       [UserController::class, 'update'])->name('update');
        Route::delete('/{user}',    [UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle', [UserController::class, 'toggle'])->name('toggle');
    });

    // ── Master Data (superadmin only) ─────────────────────────────────────
    Route::middleware('role:superadmin')->prefix('master')->name('master.')->group(function () {

        // Wilayah
        Route::resource('wilayah', Master\WilayahController::class)->except(['show','create','edit']);
        Route::patch('wilayah/{wilayah}/toggle', [Master\WilayahController::class, 'toggle'])->name('wilayah.toggle');

        // Cabang
        Route::resource('cabang', Master\CabangController::class)->except(['show','create','edit']);
        Route::patch('cabang/{cabang}/toggle', [Master\CabangController::class, 'toggle'])->name('cabang.toggle');

        // Toko
        Route::resource('toko', Master\TokoController::class)->except(['show','create','edit']);
        Route::patch('toko/{toko}/toggle', [Master\TokoController::class, 'toggle'])->name('toko.toggle');
        Route::get('toko/cabangs', [Master\TokoController::class, 'getCabangsByWilayah'])->name('toko.cabangs');

        // Brand
        Route::resource('brand', Master\BrandController::class)->except(['show','create','edit']);
        Route::patch('brand/{brand}/toggle', [Master\BrandController::class, 'toggle'])->name('brand.toggle');

        // PIC
        Route::resource('pic', Master\PicController::class)->except(['show','create','edit']);
        Route::patch('pic/{pic}/toggle', [Master\PicController::class, 'toggle'])->name('pic.toggle');
    });
});
