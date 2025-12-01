<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManufacturingOrderController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Halaman depan (public)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'      => Route::has('login'),
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

// Semua route yang butuh login
Route::middleware('auth')->group(function () {
    // Dashboard (Inertia)
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master
    Route::resource('products', ProductController::class);
    Route::resource('locations', LocationController::class);

    // Stock adjustment (manual IN/OUT)
    Route::resource('stock-transactions', StockTransactionController::class)
        ->only(['index', 'create', 'store']);

    // BOM
    Route::resource('boms', BomController::class);
    Route::post('boms/{bom}/items', [BomController::class, 'storeItem'])->name('boms.items.store');
    Route::delete('boms/{bom}/items/{item}', [BomController::class, 'destroyItem'])->name('boms.items.destroy');

    // Manufacturing Order
    Route::resource('manufacturing-orders', ManufacturingOrderController::class);
    Route::post(
        'manufacturing-orders/{manufacturingOrder}/complete',
        [ManufacturingOrderController::class, 'complete']
    )->name('manufacturing-orders.complete');
});

require __DIR__ . '/auth.php';
