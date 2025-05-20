<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\{
    PostController,
    KelasController,
    JurusanController,
    DashboardController,
    LaporanController,
    PanitiaController,
    ProfileController,
    PengumumanController,
    PendaftaranController,
    TahunAjaranController,
    AdministrasiController,
    MidtransController,
    LandingPageController
};


// Guest routes

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'verify'])->name('auth.verify');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard', ['title' => 'Admin Dashboard']);
        })->name('admin.dashboard');
    });

    // Panitia routes
    Route::middleware('panitia')->prefix('panitia')->group(function () {
        Route::get('/dashboard', function () {
            return view('panitia.dashboard', ['title' => 'Panitia Dashboard']);
        })->name('panitia.dashboard');
    });
});


// Dapat diakses tanpa login
Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');

// Dashboard
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Routes untuk Admin dan Panitia
// Pendaftaran Management
Route::prefix('pendaftaran')->group(function () {
    Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
    Route::post('/', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::get('/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::get('/{pendaftaran}/edit', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit');
    Route::put('/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
    Route::delete('/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
    Route::get('/export', [PendaftaranController::class, 'export'])->name('pendaftaran.export');
    Route::post('/{pendaftaran}/assign-kelas', [PendaftaranController::class, 'assignKelas'])->name('pendaftaran.assignKelas');
});


    Route::resource('panitia', PanitiaController::class);



Route::prefix('kelas')->group(function () {
    Route::get('/', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
    Route::get('/{kelas}/export-absensi', [KelasController::class, 'exportAbsensi'])->name('kelas.export-absensi');
    Route::get('/{kelas}/print-absensi', [KelasController::class, 'printAbsensi'])->name('kelas.print-absensi');
});


// Master Data Management
Route::resources([
    'jurusan' => JurusanController::class,
    'post' => PostController::class,
]);


Route::resource('tahun-ajaran', TahunAjaranController::class);

// Administrasi Management
Route::prefix('administrasi')->name('administrasi.')->group(function () {
    Route::get('/pembayaran', [AdministrasiController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/data', [AdministrasiController::class, 'data'])->name('pembayaran.data');
    Route::get('/pembayaran/detail/{administrasi}', [AdministrasiController::class, 'show'])->name('pembayaran.detail');
    Route::get('/pembayaran/bayar/{administrasi}', [AdministrasiController::class, 'create'])->name('pembayaran.bayar');
    Route::post('/pembayaran/bayar/{administrasi}', [AdministrasiController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/{administrasi}/struk', [AdministrasiController::class, 'struk'])->name('pembayaran.struk');

    // Midtrans payment page
    Route::get('/pembayaran/midtrans/{administrasi}', [AdministrasiController::class, 'midtransPayment'])->name('pembayaran.midtrans');
    Route::get('/pembayaran/check-status/{orderId}', [AdministrasiController::class, 'checkPaymentStatus'])->name('pembayaran.check-status');
});

// Midtrans Routes
Route::prefix('midtrans')->name('midtrans.')->group(function () {
    // Get Snap Token
    Route::post('/get-snap-token/{administrasi}', [MidtransController::class, 'getSnapToken'])->name('get-snap-token');

    // Notification URL
    Route::post('/notification', [MidtransController::class, 'notification'])->name('notification');

    // Finish, Unfinish, and Error URLs
    Route::get('/finish', [MidtransController::class, 'finish'])->name('finish');
    Route::get('/unfinish', [MidtransController::class, 'unfinish'])->name('unfinish');
    Route::get('/error', [MidtransController::class, 'error'])->name('error');

    // Check payment status
    Route::get('/status', [MidtransController::class, 'status'])->name('status');
});

// Laporan Management
Route::prefix('laporan')->name('laporan.')->group(function () {
    // Laporan Keuangan
    Route::get('/keuangan', [LaporanController::class, 'indexKeuangan'])->name('keuangan');
    Route::get('/keuangan/export', [LaporanController::class, 'exportKeuangan'])->name('export.keuangan');

    // Laporan Pendaftaran
    Route::get('/pendaftaran', [LaporanController::class, 'indexPendaftaran'])->name('pendaftaran');
    Route::get('/pendaftaran/export', [LaporanController::class, 'exportPendaftaran'])->name('pendaftaran.export');
});

// Profile Management (Semua user yang sudah login)
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
