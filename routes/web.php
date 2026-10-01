<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\surat_masuk;
use App\Http\Controllers\surat_keluar;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public (guest) routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');

Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected (auth) routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard & index
    Route::get('/surat/dashboard', [SuratController::class, 'index'])->name('surat.index');

    // Create / Store
    Route::get('/surat/create',        [SuratController::class, 'create'])->name('surat.create');
    Route::get('/surat/create-caraka', [SuratController::class, 'createCaraka'])->name('surat.create.caraka');
    Route::post('/surat',              [SuratController::class, 'store'])->name('surat.store');

    // Edit umum (judul/nomor)
    Route::get('/surat/{id_surats}/edit', [SuratController::class, 'edit'])->whereNumber('id_surats')->name('surat.edit');
    Route::put('/surat/{id_surats}',      [SuratController::class, 'update'])->whereNumber('id_surats')->name('surat.update');

    // Edit khusus Caraka
    Route::get('/surat/{surat}/edit-caraka',   [SuratController::class, 'editCaraka'])->name('surat.edit.caraka');
    Route::put('/surat/{surat}/update-caraka', [SuratController::class, 'updateCaraka'])->name('surat.update.caraka');

    // Hapus surat
    Route::delete('/surat/delete/{id_surats}', [SuratController::class, 'destroy'])->whereNumber('id_surats')->name('surat.destroy');

    // Masuk/Keluar
    Route::get('/surat/masuk',  [surat_masuk::class, 'index'])->name('surat.masuk');
    Route::get('/surat/keluar', [surat_keluar::class, 'index'])->name('surat.keluar');

    // Search
    Route::get('/surat/search', [SuratController::class, 'search'])->name('surat.search');

    // ====== Setting role (admin check dilakukan di AuthController) ======
    Route::prefix('setting_role')->name('setting.role.')->group(function () {
        Route::get('/', [AuthController::class, 'setting_role'])->name('index');
        Route::put('/{id_users}', [AuthController::class, 'update_role'])->whereNumber('id_users')->name('update');
        Route::delete('/{id_users}', [AuthController::class, 'destroy'])->whereNumber('id_users')->name('destroy');
    });

    // ---------- Bagian TU (protected) ----------
    Route::get('/surat_bagianTU_biasa', [SuratController::class, 'surat_bagianTU_biasa'])->name('surat.bagianTU_biasa');

    // Viewer (protected)
    Route::get('/lihat_surat_ketuampr/{kategori}/{id_surats}',       [SuratController::class, 'lihat_surat_ketuampr'])
        ->whereNumber('id_surats')->name('lihat.surat.ketuampr');

    Route::get('/lihat_surat_wakil_ketuampr/{kategori}/{id_surats}', [SuratController::class, 'lihat_surat_wakil_ketuampr'])
        ->whereNumber('id_surats')->name('lihat.surat.wakil.ketuampr');

    Route::get('/lihat_surat_bagian_tu/{kategori}/{id_surats}',      [SuratController::class, 'lihat_surat_bagian_tu'])
        ->whereNumber('id_surats')->name('lihat.surat.bagian_tu');
});

Route::middleware('auth')->group(function () {
    Route::get('/surat_ketua_mpr_rahasia', [SuratController::class, 'surat_ketua_mpr_rahasia'])
        ->name('surat.ketuamprrahasia');
    Route::get('/surat_ketua_mpr_sangat_rahasia', [SuratController::class, 'surat_ketua_mpr_sangat_rahasia'])
        ->name('surat.ketuamprsangatrahasia');
    Route::get('/surat/filterRahasia', [SuratController::class, 'surat_ketua_mpr_rahasia'])
        ->name('surat.filterRahasia');
    Route::get('/surat/filterSangatRahasia', [SuratController::class, 'surat_ketua_mpr_sangat_rahasia'])
        ->name('surat.filterSangatRahasia');

    Route::get('/surat_wakil_ketua_mpr_rahasia/{slug}', [SuratController::class, 'wakil_rahasia'])
        ->name('surat.wakilketuamprrahasia');
    Route::get('/surat_wakil_ketua_mpr_sangat_rahasia/{slug}', [SuratController::class, 'wakil_sangat_rahasia'])
        ->name('surat.wakilketuamprsangatrahasia');

    Route::get('/surat_bagianKearsipan_rahasia', [SuratController::class, 'surat_bagianKearsipan_rahasia'])
        ->name('surat.bagianKearsipan_rahasia');
    Route::get('/surat_bagianKearsipan_sangat_rahasia', [SuratController::class, 'surat_bagianKearsipan_sangat_rahasia'])
        ->name('surat.bagianKearsipan_sangat_rahasia');
    Route::get('/lihat_surat_bagianKearsipan/{kategori}/{id_surats}', [SuratController::class, 'lihat_surat_bagian_kearsipan'])
        ->whereNumber('id_surats')->name('lihat.surat.bagian.kearsipan');
});

/*
|--------------------------------------------------------------------------
| Public (read-only) pages
|--------------------------------------------------------------------------
*/

// Detail surat masuk/keluar (umum)
Route::get('/surat/detail/{kategori}/{id_surats}', [SuratController::class, 'showDetailsurat'])
    ->where('kategori', 'masuk|keluar')
    ->whereNumber('id_surats')
    ->name('surat.detailsurat');

Route::get('/surat/{surat}/file/{type?}', [SuratController::class, 'downloadSuratFile'])
    ->whereNumber('surat')
    ->where('type', 'document|receipt')
    ->name('surat.file');

// (hapus mismatch) — rute di bawah disamakan ke showDetailsurat agar cocok 2 parameter
Route::get('/surat/{kategori}/{id_surats}', [SuratController::class, 'showDetailsurat'])
    ->whereNumber('id_surats')
    ->name('surat.detail');

// Ketua MPR
Route::get('/surat_bagianTU', [SuratController::class, 'surat_bagianTU'])->name('surat.bagianTU');
Route::get('/surat_ketua_mpr',                [SuratController::class, 'ketua_mpr'])->name('surat.ketuampr');
Route::get('/surat_ketua_mpr_biasa',          [SuratController::class, 'surat_ketua_mpr_biasa'])->name('surat.ketuamprbiasa');

// Wakil Ketua MPR — DISAMAKAN dengan controller (wakil, wakil_biasa, wakil_rahasia, wakil_sangat_rahasia)
Route::pattern('slug', '[a-z0-9\-]+');

Route::get('/surat_wakil_ketua_mpr/{slug}',
    [SuratController::class, 'wakil'])->name('surat.wakilketuampr');

Route::get('/surat_wakil_ketua_mpr_biasa/{slug}',
    [SuratController::class, 'wakil_biasa'])->name('surat.wakilketuamprbiasa');


// (opsional) rute statis 1..8 untuk kompatibilitas lama (boleh dihapus jika tidak dipakai)
Route::get('/surat_wakil_ketua_mpr1', [SuratController::class, 'surat_wakil_ketua_mpr1'])->name('surat.wakilketuampr1');
Route::get('/surat_wakil_ketua_mpr2', [SuratController::class, 'surat_wakil_ketua_mpr2'])->name('surat.wakilketuampr2');
Route::get('/surat_wakil_ketua_mpr3', [SuratController::class, 'surat_wakil_ketua_mpr3'])->name('surat.wakilketuampr3');
Route::get('/surat_wakil_ketua_mpr4', [SuratController::class, 'surat_wakil_ketua_mpr4'])->name('surat.wakilketuampr4');
Route::get('/surat_wakil_ketua_mpr5', [SuratController::class, 'surat_wakil_ketua_mpr5'])->name('surat.wakilketuampr5');
Route::get('/surat_wakil_ketua_mpr6', [SuratController::class, 'surat_wakil_ketua_mpr6'])->name('surat.wakilketuampr6');
Route::get('/surat_wakil_ketua_mpr7', [SuratController::class, 'surat_wakil_ketua_mpr7'])->name('surat.wakilketuampr7');
Route::get('/surat_wakil_ketua_mpr8', [SuratController::class, 'surat_wakil_ketua_mpr8'])->name('surat.wakilketuampr8');

// Halaman muka & TU Pimpinan (landing)
Route::get('/halaman_muka',       [SuratController::class, 'showHalaman'])->name('halaman_muka');
Route::get('/bagian_tu_pimpinan', [SuratController::class, 'showBagianTU'])->name('bagian_tu_pimpinan');

// Filter ketua MPR (alias)
Route::get('/surat/filter',              [SuratController::class, 'surat_ketua_mpr_biasa'])->name('surat.filter');

// Bagian Kearsipan
Route::get('/surat_bagianKearsipan',                        [SuratController::class, 'surat_bagianKearsipan'])->name('surat.bagianKearsipan');
Route::get('/surat_bagianKearsipan_biasa',                  [SuratController::class, 'surat_bagianKearsipan_biasa'])->name('surat.bagianKearsipan_biasa');


