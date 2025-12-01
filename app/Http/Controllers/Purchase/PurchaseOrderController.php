<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier')->latest()->get();
        return view('purchase_orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'order_date' => 'required',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|integer|min=1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Create PO number
        $poNumber = 'PO-' . time();

        // Create Purchase Order
        $order = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'po_number' => $poNumber,
            'order_date' => $request->order_date,
            'status' => 'Draft',
            'total_amount' => 0,
            'created_by' => auth()->id(),
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $subtotal = $item['qty'] * $item['unit_price'];
            $total += $subtotal;

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $subtotal,
            ]);
        }

        // Update total
        $order->update(['total_amount' => $total]);

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created!');
    }

    public function show($id)
    {
        $order = PurchaseOrder::with('items.product', 'supplier')->findOrFail($id);
        return view('purchase_orders.show', compact('order'));
    }
}
