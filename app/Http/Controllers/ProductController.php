<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('products.*')
            ->selectSub(function ($query) {
                $query->from('stock_transactions')
                    ->selectRaw('COALESCE(SUM(CASE WHEN trx_type = "IN" THEN qty ELSE -qty END), 0)')
                    ->whereColumn('product_id', 'products.id');
            }, 'stock')
            ->orderBy('code')
            ->paginate(10);

        // Menggunakan Inertia untuk render halaman Inventory
        return Inertia::render('Modul/Inventory', [
            'products' => $products
        ]);
    }

    public function create()
    {
        // Panggil halaman Inertia untuk create product
        return Inertia::render('Modul/Inventory/ProductCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:50|unique:products,code',
            'name'      => 'required|string|max:255',
            'uom'       => 'required|string|max:20',
            'type'      => 'required|in:RAW,FINISHED',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Product::create($data);

        return redirect()
        ->route('inventory')
        ->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Modul/Inventory/ProductEdit', [
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:50|unique:products,code,' . $product->id,
            'name'      => 'required|string|max:255',
            'uom'       => 'required|string|max:20',
            'type'      => 'required|in:RAW,FINISHED',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
