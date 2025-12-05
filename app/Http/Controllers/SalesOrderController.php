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
    // List semua Sales Order
    public function index(Request $request)
    {
        $query = SalesOrder::with('items', 'customer')->orderByDesc('created_at');

        // Filter berdasarkan Tipe Halaman (Menu)
        if ($request->has('type')) {
            if ($request->type === 'quotation') {
                // Quotation: Hanya Draft & Submitted
                $query->whereIn('status', ['draft', 'submitted']);
            } elseif ($request->type === 'order') {
                // Sales Order: Hanya Approved & Rejected
                $query->whereIn('status', ['approved', 'rejected']);
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        return Inertia::render('Modul/Sales/Sales', [
            'orders' => $orders,
            'currentType' => $request->type // Kirim info tipe ke frontend
        ]);
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        // Ambil produk beserta stok saat ini
        $products = Product::withSum(['stockTransactions as total_in' => function ($q) {
            $q->where('trx_type', 'IN');
        }], 'qty')
        ->withSum(['stockTransactions as total_out' => function ($q) {
            $q->where('trx_type', 'OUT');
        }], 'qty')
        ->orderBy('name')
        ->get()
        ->map(function ($product) {
            $product->current_stock = ($product->total_in ?? 0) - ($product->total_out ?? 0);
            return $product;
        });

        return Inertia::render('Modul/Sales/SalesCreate', [
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
            'customer_id'    => 'required|exists:customers,id',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // CREATE HEADER (Default Draft)
            $so = SalesOrder::create([
                'so_number'   => $request->so_number,
                'order_date'  => $request->order_date,
                'customer_id' => $request->customer_id,
                'status'      => 'draft', // Selalu draft saat awal
                'total_amount'=> 0,
            ]);

            // INSERT ITEM
            $total = 0;
            foreach ($request->items as $item) {
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal = $qty * $price;
                $total += $subtotal;

                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id'     => $item['product_id'],
                    'qty'            => $qty,
                    'price'          => $price,
                    'subtotal'       => $subtotal,
                ]);
            }

            // UPDATE TOTAL
            $so->update(['total_amount' => $total]);

            // NOTE: Stok TIDAK dikurangi saat create (karena status Draft)

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dibuat (Draft).');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Form edit
    public function edit($id)
    {
        $order = SalesOrder::with('items.product')->findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        
        // Ambil produk beserta stok saat ini
        $products = Product::withSum(['stockTransactions as total_in' => function ($q) {
            $q->where('trx_type', 'IN');
        }], 'qty')
        ->withSum(['stockTransactions as total_out' => function ($q) {
            $q->where('trx_type', 'OUT');
        }], 'qty')
        ->orderBy('name')
        ->get()
        ->map(function ($product) {
            $product->current_stock = ($product->total_in ?? 0) - ($product->total_out ?? 0);
            return $product;
        });

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
            'customer_id'    => 'required|exists:customers,id',
            'status'         => 'required|string|in:draft,submitted,approved,rejected',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $so = SalesOrder::with('items')->findOrFail($id);
            $oldStatus = $so->status;
            $newStatus = $request->status;

            // 1. KEMBALIKAN STOK LAMA (Jika status lama 'approved' ATAU jika ada transaksi stok sebelumnya)
            // Kita cek apakah ada transaksi OUT untuk SO ini, jika ada, kita kembalikan (IN).
            // Ini menangani kasus edit SO Approved -> Draft, atau edit item SO Approved -> Approved.
            
            $existingTrx = \App\Models\StockTransaction::where('reference', $so->so_number)
                            ->where('trx_type', 'OUT')
                            ->exists();

            if ($existingTrx) {
                // Loop item lama untuk mengembalikan stok
                foreach ($so->items as $oldItem) {
                    \App\Models\StockTransaction::create([
                        'product_id'  => $oldItem->product_id,
                        'location_id' => 1,
                        'trx_type'    => 'IN', // Kembalikan stok
                        'qty'         => $oldItem->qty,
                        'trx_date'    => now(),
                        'reference'   => $so->so_number . '-REV', // Tanda revisi
                        'note'        => 'Revisi SO ' . $so->so_number,
                    ]);
                }
                
                // Hapus history transaksi lama agar tidak double counting? 
                // Tidak perlu dihapus, cukup ditimpa dengan IN (netral). 
                // Tapi agar rapi di Inventory, kita biarkan history tercatat.
            }

            // 2. UPDATE DATA SO
            $so->update([
                'order_date'  => $request->order_date,
                'customer_id' => $request->customer_id,
                'status'      => $newStatus,
            ]);

            // Hapus item lama di database
            SalesOrderItem::where('sales_order_id', $id)->delete();

            // 3. INSERT ITEM BARU & POTONG STOK BARU (Jika status 'approved')
            $total = 0;
            foreach ($request->items as $item) {
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal = $qty * $price;
                $total += $subtotal;

                SalesOrderItem::create([
                    'sales_order_id' => $id,
                    'product_id'     => $item['product_id'],
                    'qty'            => $qty,
                    'price'          => $price,
                    'subtotal'       => $subtotal,
                ]);

                // Jika status baru APPROVED, potong stok
                if ($newStatus === 'approved') {
                    \App\Models\StockTransaction::create([
                        'product_id'  => $item['product_id'],
                        'location_id' => 1,
                        'trx_type'    => 'OUT',
                        'qty'         => $qty,
                        'trx_date'    => $request->order_date,
                        'reference'   => $so->so_number,
                        'note'        => 'Sales Order ' . $so->so_number,
                    ]);
                }
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
            $so = SalesOrder::with('items')->findOrFail($id);

            // Cek apakah perlu mengembalikan stok
            // Jika status approved ATAU ada transaksi stok terkait
            $existingTrx = \App\Models\StockTransaction::where('reference', $so->so_number)
                            ->where('trx_type', 'OUT')
                            ->exists();

            if ($existingTrx) {
                foreach ($so->items as $item) {
                    \App\Models\StockTransaction::create([
                        'product_id'  => $item->product_id,
                        'location_id' => 1,
                        'trx_type'    => 'IN', // Kembalikan stok
                        'qty'         => $item->qty,
                        'trx_date'    => now(),
                        'reference'   => $so->so_number . '-DEL',
                        'note'        => 'Pembatalan SO ' . $so->so_number,
                    ]);
                }
            }

            SalesOrderItem::where('sales_order_id', $id)->delete();
            $so->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dihapus!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Cetak (Print View)
    public function print($id)
    {
        $order = SalesOrder::with('items.product', 'customer')->findOrFail($id);
        return view('print.sales_order', compact('order'));
    }
}

