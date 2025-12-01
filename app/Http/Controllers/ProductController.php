<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('code')->paginate(10);

        // Masih pakai Blade, tidak diubah dulu
        return view('products.index', compact('products'));
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
        return view('products.edit', compact('product'));
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
