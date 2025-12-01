<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function index()
    {
        $transactions = StockTransaction::with('product', 'location')
            ->orderByDesc('trx_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('stock_transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('stock_transactions.create', compact('products', 'locations'));
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

        return redirect()->route('stock-transactions.index')->with('success', 'Stock transaction created.');
    }
}
