<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Invoice;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'sales_count' => SalesOrder::count(),
                'sales_total' => SalesOrder::where('status', 'approved')->sum('total_amount'),
                'purchase_pending' => PurchaseOrder::whereIn('status', ['RFQ', 'Sent'])->count(),
                'products_count' => Product::count(),
                // Asumsi ada kolom min_stock_level, jika tidak ada kita pakai angka fix dulu atau skip
                'products_active' => Product::count(), 
                'unpaid_invoices' => Invoice::where('status', 'posted')->count(),
            ]
        ]);
    }
}
