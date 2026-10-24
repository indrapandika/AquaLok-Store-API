<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE PUBLIK: TIDAK MEMERLUKAN TOKEN
// ==========================================

// Daftar kategori
Route::get('/categories', function () {
    return response()->json([
        'success' => true,
        'message' => 'Daftar kategori AquaLok Store.',
        'data' => Category::query()
            ->orderBy('category_id')
            ->get(['category_id', 'category']),
    ]);
})->name('api.categories.index');

// Daftar produk, pencarian, dan filter
Route::get('/products', [
    ProductController::class,
    'index'
])->name('api.products.index');

// Detail produk
Route::get('/products/{id}', [
    ProductController::class,
    'show'
])->name('api.products.show');

// Register dan login
Route::post('/register', [
    AuthController::class,
    'register'
])->name('api.register');

Route::post('/login', [
    AuthController::class,
    'login'
])->name('api.login');


// ==========================================
// ROUTE YANG MEMERLUKAN TOKEN SANCTUM
// ==========================================

Route::middleware('auth:sanctum')->group(function () {

    // Melihat profil pengguna yang login
    Route::get('/profile', [
        AuthController::class,
        'profile'
    ])->name('api.profile');

    // Logout
    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('api.logout');


    // ======================================
    // ROUTE KHUSUS ADMIN
    // Memerlukan token dan role admin
    // ======================================

    Route::middleware('admin')->group(function () {

        // Menambahkan produk
        Route::post('/products', [
            ProductController::class,
            'store'
        ])->name('api.products.store');

        // Memperbarui produk
        Route::match(['put', 'patch'], '/products/{id}', [
            ProductController::class,
            'update'
        ])->name('api.products.update');

        // Menghapus produk
        Route::delete('/products/{id}', [
            ProductController::class,
            'destroy'
        ])->name('api.products.destroy');

    });

});