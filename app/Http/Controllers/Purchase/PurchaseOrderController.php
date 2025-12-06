<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // Tampilkan semua PO
    public function index()
    {
        $orders = PurchaseOrder::with('items')->get();
        return view('purchase.index', compact('orders'));
    }

    // Form tambah
    public function create()
    {
        return view('purchase.create');
    }

    // Simpan data
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $po = PurchaseOrder::create([
                'po_number'   => $request->po_number,
                'order_date'  => $request->order_date,
                'supplier_id' => $request->supplier_id,
                'status'      => 'draft',
                'total_amount'=> 0,
            ]);

            $total = 0;
            foreach ($request->items as $i => $item) {
                $subtotal = $request->qty[$i] * $request->price[$i];
                $total += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $request->product_id[$i],
                    'qty' => $request->qty[$i],
                    'price' => $request->price[$i],
                    'subtotal' => $subtotal,
                ]);
            }

            $po->update(['total_amount' => $total]);

            DB::commit();

            return redirect()->route('purchase.index')->with('success', 'Purchase Order dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Form Edit
    public function edit($id)
    {
        $order = PurchaseOrder::with('items')->findOrFail($id);
        return view('purchase.edit', compact('order'));
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
