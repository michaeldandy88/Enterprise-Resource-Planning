<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'SALES'); // Default SALES

        $invoices = Invoice::with(['customer', 'supplier'])
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Modul/Invoice/Index', [
            'invoices' => $invoices,
            'currentType' => $type
        ]);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['items.product', 'customer', 'supplier', 'salesOrder', 'purchaseOrder'])->findOrFail($id);
        return Inertia::render('Modul/Invoice/Show', [
            'invoice' => $invoice
        ]);
    }

    public function createFromSalesOrder($salesOrderId)
    {
        DB::beginTransaction();
        try {
            $so = SalesOrder::with('items')->findOrFail($salesOrderId);

            // Cek apakah sudah ada invoice untuk SO ini
            $existingInvoice = Invoice::where('sales_order_id', $salesOrderId)->first();
            if ($existingInvoice) {
                return redirect()->route('invoices.show', $existingInvoice->id)->with('info', 'Invoice sudah ada untuk SO ini.');
            }

            // Generate Nomor Invoice (Simple)
            $invNumber = 'INV-SO/' . date('Y') . '/' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create Header
            $invoice = Invoice::create([
                'invoice_number' => $invNumber,
                'type'           => 'SALES',
                'invoice_date'   => now(),
                'due_date'       => now()->addDays(30), // Default TOP 30 hari
                'customer_id'    => $so->customer_id,
                'sales_order_id' => $so->id,
                'status'         => 'draft',
                'total_amount'   => $so->total_amount,
            ]);

            // Create Items
            foreach ($so->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                    'price'      => $item->price,
                    'subtotal'   => $item->subtotal,
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice berhasil dibuat dari Sales Order.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function createFromPurchaseOrder($purchaseOrderId)
    {
        DB::beginTransaction();
        try {
            $po = \App\Models\PurchaseOrder::with('items')->findOrFail($purchaseOrderId);

            // Cek apakah sudah ada invoice untuk PO ini
            $existingInvoice = Invoice::where('purchase_order_id', $purchaseOrderId)->first();
            if ($existingInvoice) {
                return redirect()->route('invoices.show', $existingInvoice->id)->with('info', 'Bill sudah ada untuk PO ini.');
            }

            // Generate Nomor Bill
            $invNumber = 'BILL-PO/' . date('Y') . '/' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create Header
            $invoice = Invoice::create([
                'invoice_number' => $invNumber,
                'type'           => 'PURCHASE',
                'invoice_date'   => now(),
                'due_date'       => now()->addDays(30),
                'supplier_id'    => $po->supplier_id,
                'purchase_order_id' => $po->id,
                'status'         => 'draft',
                'total_amount'   => $po->total_amount,
            ]);

            // Create Items
            foreach ($po->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                    'price'      => $item->unit_price,
                    'subtotal'   => $item->qty * $item->unit_price,
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Vendor Bill berhasil dibuat dari Purchase Order.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:posted,paid',
            'payment_date' => 'nullable|required_if:status,paid|date',
            'payment_method' => 'nullable|required_if:status,paid|string',
        ]);

        $invoice = Invoice::findOrFail($id);
        
        $data = ['status' => $request->status];
        
        if ($request->status === 'paid') {
            $data['payment_date'] = $request->payment_date;
            $data['payment_method'] = $request->payment_method;
        }

        $invoice->update($data);

        return redirect()->back()->with('success', 'Status Invoice berhasil diperbarui.');
    }
}
