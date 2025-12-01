<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BomController extends Controller
{
    public function index()
    {
         $boms = Bom::with('product')->orderBy('name')->paginate(10);

        return Inertia::render('Modul/Manufacturing/BomIndex', [
            'boms' => $boms,
        ]);
    }

    public function create()
    {
        $finishedProducts = Product::where('type', 'FINISHED')
            ->orderBy('name')
            ->get();

        return Inertia::render('Modul/Manufacturing/BomCreate', [
            'finished_products' => $finishedProducts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name'       => 'required|string|max:255',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Bom::create($data);

        // setelah create BOM, balik ke modul manufacturing
        return redirect()
            ->route('manufacturing')
            ->with('success', 'BOM created.');
    }

    public function edit(Bom $bom)
    {
        $bom->load('items.product');

        $rawProducts = Product::where('type', 'RAW')
            ->orderBy('name')
            ->get();

        return Inertia::render('Modul/Manufacturing/BomEdit', [
            'bom'          => $bom,
            'raw_products' => $rawProducts,
        ]);
    }

    public function update(Request $request, Bom $bom)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $bom->update($data);

        return redirect()
            ->route('manufacturing')
            ->with('success', 'BOM updated.');
    }

    public function destroy(Bom $bom)
    {
        $bom->delete();

        return redirect()
        ->route('boms.index')
        ->with('success', 'BOM deleted.');
    }

    // ====== ITEM HANDLING ======

    public function storeItem(Request $request, Bom $bom)
    {
        $data = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'qty_per_unit' => 'required|numeric|min:0.0001',
        ]);

        $data['bom_id'] = $bom->id;

        BomItem::create($data);

        return redirect()
            ->route('boms.edit', $bom)
            ->with('success', 'BOM item added.');
    }

    public function destroyItem(Bom $bom, BomItem $item)
    {
        if ($item->bom_id !== $bom->id) {
            abort(404);
        }

        $item->delete();

        // Tetap di halaman edit BOM, biar enak
        return redirect()
            ->route('boms.edit', $bom)
            ->with('success', 'BOM item deleted.');
    }
}
