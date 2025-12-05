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
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SalesOrderController;

// Route untuk halaman utama (root)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

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

    Route::get('/inventory', function () {
        return Inertia::render('Modul/Inventory', [
            'transactions' => \App\Models\StockTransaction::with('product', 'location')
                ->orderByDesc('trx_date')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    })->name('inventory');

    Route::get('/purchase', fn () => Inertia::render('Modul/Purchase'))->name('purchase');
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
    Route::post(
        'manufacturing-orders/{manufacturingOrder}/complete',
        [ManufacturingOrderController::class, 'complete']
    )->name('manufacturing-orders.complete');

    // HaKiem
    // Route::prefix('purchase')->group(function () {
    // Route::get('/', [PurchaseOrderController::class, 'index'])->name('purchase.index');
    // Route::get('/create', [PurchaseOrderController::class, 'create'])->name('purchase.create');
    // Route::post('/store', [PurchaseOrderController::class, 'store'])->name('purchase.store');
    // Route::get('/edit/{id}', [PurchaseOrderController::class, 'edit'])->name('purchase.edit');
    // Route::post('/update/{id}', [PurchaseOrderController::class, 'update'])->name('purchase.update');
    // Route::delete('/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase.delete');
    // });
   
    Route::prefix('sales')->group(function () {
        Route::get('/', [SalesOrderController::class, 'index'])->name('sales.index');
        Route::get('/create', [SalesOrderController::class, 'create'])->name('sales.create');
        Route::post('/store', [SalesOrderController::class, 'store'])->name('sales.store');
        Route::get('/edit/{id}', [SalesOrderController::class, 'edit'])->name('sales.edit');
        Route::post('/update/{id}', [SalesOrderController::class, 'update'])->name('sales.update');
        Route::delete('/{id}', [SalesOrderController::class, 'destroy'])->name('sales.delete');
    });

});
require __DIR__ . '/auth.php';