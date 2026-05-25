<?php
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// --- RUTE PUBLIC (Bisa diakses siapa saja tanpa login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Buku
Route::get('/buku', [BukuController::class, 'index']);
Route::get('/buku/{id}', [BukuController::class, 'show']);

// --- RUTE PROTECTED (Wajib Login / Bawa Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth Logout
    Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/buku', [BukuController::class, 'store']);
Route::put('/buku/{id}', [BukuController::class, 'update']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

// Rute Peminjaman
Route::get('/peminjaman', [PeminjamanController::class, 'index']);           // Select All
Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show']);       // Select 1 Berdasarkan ID
Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update']);     // Update Status Pinjam/Kembali 
Route::post('/peminjaman', [PeminjamanController::class, 'store']);          // Insert Transaksi
Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy']); // Delete Transaksi

// Rute Detail Peminjaman
Route::get('/peminjaman/detail/all', [PeminjamanController::class, 'indexDetail']);             // Select ALL
Route::post('/peminjaman/{id_transaksi}/detail', [PeminjamanController::class, 'storeDetail']); // Insert Detail
Route::delete('/peminjaman/detail/{id_detail}', [PeminjamanController::class, 'destroyDetail']); // Delete Detail

// Statistik
Route::get('/statistik', [PeminjamanController::class, 'statistik']);
Route::get('/statistik/peminjam-terbanyak', [PeminjamanController::class, 'peminjamTerbanyak']); // Top Peminjam 
Route::get('/statistik/buku-terbanyak', [PeminjamanController::class, 'bukuTerbanyak']);    // Statistik: Buku Paling Banyak Dipinjam 

});