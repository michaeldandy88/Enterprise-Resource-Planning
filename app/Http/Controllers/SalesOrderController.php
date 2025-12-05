<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Product;
use Inertia\Inertia;

class SalesOrderController extends Controller
{
    // List semua Sales Order
public function index()
{
    $orders = SalesOrder::with('items', 'customer')->get();

    return Inertia::render('Modul/Sales/Sales', [
        'orders' => $orders,
    ]);
}
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();

        return Inertia::render('Modul/Sales/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }
    // Simpan data baru
    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'so_number'      => 'required|unique:sales_orders,so_number',
            'order_date'     => 'required|date',
            'customer_id'    => 'required|exists:users,id',
            'product_id'     => 'required|array',
            'product_id.*'   => 'required|exists:products,id',
            'qty'            => 'required|array',
            'qty.*'          => 'required|numeric|min:1',
            'price'          => 'required|array',
            'price.*'        => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // CREATE HEADER
            $so = SalesOrder::create([
                'so_number'   => $request->so_number,
                'order_date'  => $request->order_date,
                'customer_id' => $request->customer_id,
                'status'      => 'Draft',
                'total_amount'=> 0,
            ]);

            // INSERT ITEM
            $total = 0;
            foreach ($request->product_id as $i => $product) {
                $subtotal = $request->qty[$i] * $request->price[$i];
                $total += $subtotal;

                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id'     => $product,
                    'qty'            => $request->qty[$i],
                    'price'          => $request->price[$i],
                    'subtotal'       => $subtotal,
                ]);
            }

            // UPDATE TOTAL
            $so->update(['total_amount' => $total]);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Form edit
   public function edit($id)
    {
        $order = SalesOrder::with('items.product')->findOrFail($id);
        $customers = Customer::all();
        $products = Product::all();

        return Inertia::render('Modul/Sales/Edit', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products,
        ]);
    }


    // Proses update
    public function update(Request $request, $id)
    {
        // VALIDASI
        $request->validate([
            'order_date'     => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'product_id'     => 'required|array',
            'product_id.*'   => 'required|exists:products,id',
            'qty'            => 'required|array',
            'qty.*'          => 'required|numeric|min:1',
            'price'          => 'required|array',
            'price.*'        => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $so = SalesOrder::findOrFail($id);

            // Update HEADER
            $so->update([
                'order_date'  => $request->order_date,
                'customer_id' => $request->customer_id,
                'status'      => $request->status ?? $so->status,
            ]);

            // Hapus item lama
            SalesOrderItem::where('sales_order_id', $id)->delete();

            // Insert ulang item baru
            $total = 0;
            foreach ($request->product_id as $i => $product) {
                $subtotal = $request->qty[$i] * $request->price[$i];
                $total += $subtotal;

                SalesOrderItem::create([
                    'sales_order_id' => $id,
                    'product_id'     => $product,
                    'qty'            => $request->qty[$i],
                    'price'          => $request->price[$i],
                    'subtotal'       => $subtotal,
                ]);
            }

            // Update total baru
            $so->update(['total_amount' => $total]);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sales Order berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Hapus
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            SalesOrderItem::where('sales_order_id', $id)->delete();
            SalesOrder::findOrFail($id)->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dihapus!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
