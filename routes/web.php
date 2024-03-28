<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\KontrakAdendumController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Permission
    Route::resource('akses', PermissionController::class);
    Route::post('akses/load', [PermissionController::class, 'load'])->name('akses.load');

    // Role
    Route::resource('peran', RoleController::class);
    Route::post('peran/load', [RoleController::class, 'load'])->name('peran.load');

    // User
    Route::resource('user', UserController::class);
    Route::post('user/load', [UserController::class, 'load'])->name('user.load');

    // Kontrak
    Route::get('kontrak', [KontrakController::class, 'index'])->name('kontrak.index');
    Route::post('kontrak/load', [KontrakController::class, 'load'])->name('kontrak.load');
    Route::get('kontrak/create', [KontrakController::class, 'create'])->name('kontrak.create');
    Route::post('kontrak/store', [KontrakController::class, 'store'])->name('kontrak.store');
    Route::get('kontrak/{id}/{kd_skpd}/edit', [KontrakController::class, 'edit'])->name('kontrak.edit');
    Route::post('kontrak/update', [KontrakController::class, 'update'])->name('kontrak.update');
    Route::post('kontrak/delete', [KontrakController::class, 'delete'])->name('kontrak.delete');

    // KONTRAK ADENDUM
    Route::get('kontrak_adendum', [KontrakAdendumController::class, 'index'])->name('kontrak_adendum.index');
    Route::post('kontrak_adendum/load', [KontrakAdendumController::class, 'load'])->name('kontrak_adendum.load');
    Route::get('kontrak_adendum/create', [KontrakAdendumController::class, 'create'])->name('kontrak_adendum.create');
    Route::post('kontrak_adendum/store', [KontrakAdendumController::class, 'store'])->name('kontrak_adendum.store');
    Route::get('kontrak_adendum/{id}/{kd_skpd}/edit', [KontrakAdendumController::class, 'edit'])->name('kontrak_adendum.edit');
    Route::post('kontrak_adendum/update', [KontrakAdendumController::class, 'update'])->name('kontrak_adendum.update');
    Route::post('kontrak_adendum/delete', [KontrakAdendumController::class, 'delete'])->name('kontrak_adendum.delete');

    // DATA
    Route::post('kode_sub_kegiatan', [DataController::class, 'kodeSubKegiatan'])->name('kode_sub_kegiatan');
    Route::post('rekening', [DataController::class, 'rekening'])->name('rekening');
    Route::post('kode_barang', [DataController::class, 'kodeBarang'])->name('kode_barang');
    Route::post('sumber_dana', [DataController::class, 'sumberDana'])->name('sumber_dana');
    Route::post('detail_kontrak', [DataController::class, 'detailKontrak'])->name('detail_kontrak');
});

require __DIR__ . '/auth.php';
