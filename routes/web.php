<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/manufacturing', fn () => Inertia::render('Modul/Manufacturing'))->name('manufacturing');
    Route::get('/inventory', fn () => Inertia::render('Modul/Inventory'))->name('inventory');
    Route::get('/purchase', fn () => Inertia::render('Modul/Purchase'))->name('purchase');
    Route::get('/sales', fn () => Inertia::render('Modul/Sales'))->name('sales');
    Route::get('/invoicing', fn () => Inertia::render('Modul/Invoicing'))->name('invoicing');
    Route::get('/employee', fn () => Inertia::render('Modul/Employee'))->name('employee');
});

require __DIR__.'/auth.php';
