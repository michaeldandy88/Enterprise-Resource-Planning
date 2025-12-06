<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockTransactionController extends Controller
{
    public function index()
    {
        // 1. List Transaksi
        $transactions = StockTransaction::with('product', 'location')
            ->orderByDesc('trx_date')
            ->orderByDesc('id')
            ->paginate(10);

        return Inertia::render('Modul/Inventory/History', [
            'transactions' => $transactions,
        ]);
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return Inertia::render('Modul/Inventory/Create', [
            'products'  => $products,
            'locations' => $locations,
            'today'     => now()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'  => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'trx_type'    => 'required|in:IN,OUT',
            'qty'         => 'required|numeric|min:0.001',
            'trx_date'    => 'required|date',
            'reference'   => 'nullable|string|max:100',
            'note'        => 'nullable|string',
        ]);

        StockTransaction::create($data);

        // Setelah create, balik ke modul inventory (Inertia)
        return redirect()
            ->route('inventory')
            ->with('success', 'Stock transaction created.');
    }
}
