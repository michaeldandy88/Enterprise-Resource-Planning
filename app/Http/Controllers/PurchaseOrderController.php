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
    // Tampilkan semua PO dengan filter Tab
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'rfq'); // Default ke RFQ

        $query = PurchaseOrder::with(['items', 'supplier'])
            ->orderByDesc('created_at');

        if ($tab === 'po') {
            // Tab Purchase Orders
            $query->whereIn('status', ['Purchase Order', 'Received', 'Done', 'Cancelled']);
        } else {
            // Tab RFQ (Default)
            $query->whereIn('status', ['RFQ', 'Sent']);
        }

        $orders = $query->paginate(10)->withQueryString();

        return Inertia::render('Modul/Purchase/Purchase', [
            'orders' => $orders,
            'currentTab' => $tab,
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
        $order = PurchaseOrder::with('items')->findOrFail($id);

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

    // Update Status: Sent
    public function markAsSent($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        
        if ($po->status === 'RFQ') {
            $po->update(['status' => 'Sent']);

            // Kirim Email jika ada
            if ($po->supplier && $po->supplier->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($po->supplier->email)->send(new \App\Mail\PurchaseOrderMail($po));
                    return back()->with('success', 'Status diubah menjadi Sent & Email terkirim ke vendor.');
                } catch (\Exception $e) {
                    return back()->with('success', 'Status Sent, tapi gagal kirim email: ' . $e->getMessage());
                }
            } else {
                return back()->with('success', 'Status diubah menjadi Sent (Vendor tidak memiliki email).');
            }
        }
        
        return back();
    }

    // Update Status: Confirm (RFQ/Sent -> Purchase Order)
    public function confirm($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if (in_array($po->status, ['RFQ', 'Sent'])) {
            $po->update(['status' => 'Purchase Order']);
        }
        return back()->with('success', 'PO berhasil dikonfirmasi.');
    }

    // Update Status: Receive (Purchase Order -> Received) + Tambah Stok
    public function receive($id)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);

        if ($po->status === 'Purchase Order') {
            DB::transaction(function () use ($po) {
                // 1. Update Status PO
                $po->update(['status' => 'Received']);

                // 2. Catat Transaksi Stok Masuk (IN)
                foreach ($po->items as $item) {
                    \App\Models\StockTransaction::create([
                        'product_id'    => $item->product_id,
                        'trx_date'      => now(),
                        'trx_type'      => 'IN', // Barang Masuk
                        'qty'           => $item->qty,
                        'document_type' => 'PO',
                        'document_id'   => $po->id,
                        'notes'         => 'Penerimaan Barang PO #' . $po->po_number,
                        // Asumsi lokasi default ID 1 (Gudang Utama) jika belum ada fitur Multi-Location di PO
                        'location_id'   => 1, 
                    ]);
                }
            });
        }

        return back()->with('success', 'Barang diterima. Stok telah ditambahkan.');
    }

    // Delete
    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if ($po->status === 'RFQ') {
            $po->items()->delete(); // Hapus item dulu
            $po->delete();
            return redirect()->route('purchase.index')->with('success', 'PO dihapus');
        }
        return back()->with('error', 'Hanya status RFQ yang bisa dihapus.');
    }

    // Print PDF (View Mode)
    public function print($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        
        return view('pdf.purchase_order', ['order' => $po]);
    }

    // Preview Email (JSON)
    public function previewEmail($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items.product'])->findOrFail($id);
        
        // Render view email ke string HTML
        $html = view('emails.purchase_order', ['order' => $po])->render();

        return response()->json([
            'to' => $po->supplier->email ?? 'Email Vendor Tidak Tersedia',
            'subject' => 'Request for Quotation - ' . $po->po_number,
            'html' => $html
        ]);
    }

    // Cancel PO
    public function cancel($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        
        // Hanya bisa cancel jika belum diterima (Received) atau Selesai
        if (in_array($po->status, ['Received', 'Done', 'Cancelled'])) {
            return back()->with('error', 'Pesanan ini tidak bisa dibatalkan lagi.');
        }

        $po->update(['status' => 'Cancelled']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
