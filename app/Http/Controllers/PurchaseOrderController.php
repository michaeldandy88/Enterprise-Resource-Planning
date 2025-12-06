<?php

namespace App\Http\Controllers;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Supplier;
use App\Models\Product;


class PurchaseOrderController extends Controller
{
    // Tampilkan semua PO
   public function index()
{
    $orders = PurchaseOrder::with(['items', 'supplier'])->get();

    return Inertia::render('Modul/Purchase/Purchase', [
        'orders' => $orders,
    ]);
}


    // Form tambah
    public function create()
    {
        return Inertia::render('Modul/Purchase/PurchaseCreate', [
            'suppliers' => Supplier::all(),
            'products'  => Product::all(),
        ]);
    }


    // Simpan data
    public function store(Request $request)
{
    $request->validate([
        'po_number'   => 'required|unique:purchase_orders,po_number',
        'supplier_id' => 'required|exists:suppliers,id',
        'order_date'  => 'required|date',
        'items'       => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.qty'        => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        $po = PurchaseOrder::create([
            'po_number'   => $request->po_number,
            'supplier_id' => $request->supplier_id,
            'order_date'  => $request->order_date,
            'total_amount'=> 0,
            'status'      => 'RFQ',
            'created_by'  => auth()->id(),
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $subtotal = $item['qty'] * $item['unit_price'];
            $total += $subtotal;

            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id'        => $item['product_id'],
                'qty'               => $item['qty'],
                'unit_price'        => $item['unit_price'],
                'subtotal'          => $subtotal,
            ]);
        }

        $po->update(['total_amount' => $total]);

        DB::commit();
        return redirect()->route('purchase.index')
                         ->with('success', 'RFQ berhasil dibuat!');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}


    // Form Edit
    public function edit($id)
    {
        return Inertia::render('Modul/Purchase/PurchaseEdit', [
    'order'     => $order,
    'suppliers' => Supplier::all(),
    'products'  => Product::all(),
]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update($request->only(['order_date', 'supplier_id', 'status']));
        return redirect()->route('purchase.index')->with('success', 'PO diperbarui');
    }

    // Delete
    public function destroy($id)
    {
        PurchaseOrder::findOrFail($id)->delete();
        return redirect()->route('purchase.index')->with('success', 'PO dihapus');
    }
}
