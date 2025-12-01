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

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modules (Inertia)
    Route::get('/manufacturing', function () {
        return Inertia::render('Modul/Manufacturing', [
            'orders' => \App\Models\ManufacturingOrder::with('product', 'bom')
                ->orderByDesc('created_at')
                ->paginate(10),
        ]);
    })->name('manufacturing');

    Route::get('/inventory', [StockTransactionController::class, 'index'])->name('inventory');

    Route::get('/purchase', fn () => Inertia::render('Modul/Purchase'))->name('purchase');
    Route::get('/sales', fn () => Inertia::render('Modul/Sales'))->name('sales');
    Route::get('/invoicing', fn () => Inertia::render('Modul/Invoicing'))->name('invoicing');
    Route::get('/employee', fn () => Inertia::render('Modul/Employee'))->name('employee');

    // Master
    Route::resource('products', ProductController::class);
    Route::resource('locations', LocationController::class);

    // Stock manual IN/OUT
    Route::resource('stock-transactions', StockTransactionController::class)
        ->only(['index','create','store']);

    // BOM
    Route::resource('boms', BomController::class);
    Route::post('boms/{bom}/items', [BomController::class, 'storeItem'])->name('boms.items.store');
    Route::delete('boms/{bom}/items/{item}', [BomController::class, 'destroyItem'])->name('boms.items.destroy');

    // Manufacturing Order
    Route::resource('manufacturing-orders', ManufacturingOrderController::class);
    Route::post('manufacturing-orders/{manufacturingOrder}/complete', [ManufacturingOrderController::class, 'complete'])
        ->name('manufacturing-orders.complete');
});

require __DIR__.'/auth.php';
