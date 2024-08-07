<?php

use App\Http\Controllers\BastController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\KontrakAdendumController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\LaporanKontrakController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    return view('auth.login2');
})->middleware('guest')->name('home');

Route::get('/dashboard', [DataController::class, 'indexDashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::post('dashboard_data', [DataController::class, 'dataDashboard'])->name('dashboard.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // GANTI SKPD
    Route::get('ubah_skpd', [UserController::class, 'ubahSkpd'])->name('ubah_skpd.index');
    Route::post('simpan_ubah_skpd', [UserController::class, 'simpanUbahSkpd'])->name('ubah_skpd.store');

    // UBAH PASSWORD
    Route::get('ubah_password', [UserController::class, 'ubahPassword'])->name('ubah_password.index');
    Route::post('simpan_ubah_password', [UserController::class, 'simpanUbahPassword'])->name('ubah_password.store');

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

    // DATA RINCIAN KONTRAK TAMBAH KONTRAK AWAL
    Route::post('rincian_kontrak', [KontrakController::class, 'rincianKontrak'])->name('rincian_kontrak');
    Route::post('simpan_rincian_kontrak', [KontrakController::class, 'simpanRincianKontrak'])->name('rincian_kontrak.simpan');
    Route::post('hapus_rincian_kontrak', [KontrakController::class, 'hapusRincianKontrak'])->name('rincian_kontrak.hapus');

    // DATA DETAIL RINCIAN KONTRAK TAMBAH KONTRAK AWAL
    Route::post('detail_rincian_kontrak', [KontrakController::class, 'detailRincianKontrak'])->name('detail_rincian_kontrak');
    Route::post('simpan_detail_rincian_kontrak', [KontrakController::class, 'simpanDetailRincianKontrak'])->name('detail_rincian_kontrak.simpan');
    Route::post('hapus_detail_rincian_kontrak', [KontrakController::class, 'hapusDetailRincianKontrak'])->name('detail_rincian_kontrak.hapus');

    // KONTRAK ADENDUM
    Route::get('kontrak_adendum', [KontrakAdendumController::class, 'index'])->name('kontrak_adendum.index');
    Route::post('kontrak_adendum/load', [KontrakAdendumController::class, 'load'])->name('kontrak_adendum.load');
    Route::get('kontrak_adendum/create', [KontrakAdendumController::class, 'create'])->name('kontrak_adendum.create');
    Route::post('kontrak_adendum/store', [KontrakAdendumController::class, 'store'])->name('kontrak_adendum.store');
    Route::get('kontrak_adendum/{id}/{nomor}/{kd_skpd}/edit', [KontrakAdendumController::class, 'edit'])->name('kontrak_adendum.edit');
    Route::post('kontrak_adendum/update', [KontrakAdendumController::class, 'update'])->name('kontrak_adendum.update');
    Route::post('kontrak_adendum/delete', [KontrakAdendumController::class, 'delete'])->name('kontrak_adendum.delete');

    // DATA RINCIAN KONTRAK ADENDUM
    Route::post('rincian_kontrak_adendum', [KontrakAdendumController::class, 'rincianKontrak'])->name('rincian_kontrak_adendum');
    Route::post('simpan_rincian_kontrak_adendum', [KontrakAdendumController::class, 'simpanRincianKontrak'])->name('rincian_kontrak_adendum.simpan');
    Route::post('hapus_rincian_kontrak_adendum', [KontrakAdendumController::class, 'hapusRincianKontrak'])->name('rincian_kontrak_adendum.hapus');

    // DATA DETAIL RINCIAN KONTRAK TAMBAH KONTRAK ADENDUM
    Route::post('detail_rincian_kontrak_adendum', [KontrakAdendumController::class, 'detailRincianKontrak'])->name('detail_rincian_kontrak_adendum');
    Route::post('simpan_detail_rincian_kontrak_adendum', [KontrakAdendumController::class, 'simpanDetailRincianKontrak'])->name('detail_rincian_kontrak_adendum.simpan');
    Route::post('hapus_detail_rincian_kontrak_adendum', [KontrakAdendumController::class, 'hapusDetailRincianKontrak'])->name('detail_rincian_kontrak_adendum.hapus');

    // BAST
    Route::get('bast', [BastController::class, 'index'])->name('bast.index');
    Route::post('bast/load', [BastController::class, 'load'])->name('bast.load');
    Route::get('bast/create', [BastController::class, 'create'])->name('bast.create');
    Route::post('bast/store', [BastController::class, 'store'])->name('bast.store');
    Route::get('bast/{nomorbapbast}/{kd_skpd}/{idkontrak}/{nomorkontrak}/edit', [BastController::class, 'edit'])->name('bast.edit');
    Route::post('bast/update', [BastController::class, 'update'])->name('bast.update');
    Route::post('bast/delete', [BastController::class, 'delete'])->name('bast.delete');

    Route::post('cek_rincian_bast', [BastController::class, 'cekRincianBast'])->name('cek_rincian_bast');

    // DATA KONTRAK
    Route::post('kode_sub_kegiatan', [DataController::class, 'kodeSubKegiatan'])->name('kode_sub_kegiatan');
    Route::post('rekening', [DataController::class, 'rekening'])->name('rekening');
    Route::post('kode_barang', [DataController::class, 'kodeBarang'])->name('kode_barang');
    Route::post('sumber_dana', [DataController::class, 'sumberDana'])->name('sumber_dana');
    Route::post('detail_kontrak', [DataController::class, 'detailKontrak'])->name('detail_kontrak');
    Route::post('data_adendum', [DataController::class, 'dataAdendum'])->name('data_adendum');
    Route::post('cek_kontrak', [DataController::class, 'cekKontrak'])->name('cek_kontrak');
    Route::post('daftarAnggaran', [DataController::class, 'cekAnggaran'])->name('daftarAnggaran');

    // DATA BAST/BAP/PESANAN
    Route::post('kegiatan_bast', [DataController::class, 'kegiatanBast'])->name('kegiatan_bast');
    Route::post('rekening_bast', [DataController::class, 'rekeningBast'])->name('rekening_bast');
    Route::post('barang_bast', [DataController::class, 'barangBast'])->name('barang_bast');
    Route::post('sumber_bast', [DataController::class, 'sumberBast'])->name('sumber_bast');
    Route::post('realisasi_bast', [DataController::class, 'realisasiBast'])->name('realisasi_bast');

    // LAPORAN KONTRAK
    Route::get('laporan-kontrak', [LaporanKontrakController::class, 'index'])->name('laporan_kontrak.index');
    Route::get('pengadaan-kontrak', [LaporanKontrakController::class, 'cetakPengadaan'])->name('laporan_kontrak.pengadaan');
    Route::get('ringkasan-kontrak', [LaporanKontrakController::class, 'cetakRingkasan'])->name('laporan_kontrak.ringkasan');
});

Route::get('/{any}', function () {
    return view('template.404');
})->where('any', '.*');

require __DIR__ . '/auth.php';
