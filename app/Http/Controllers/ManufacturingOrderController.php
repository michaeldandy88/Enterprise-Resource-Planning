<?php

namespace App\Http\Controllers;

use App\Models\ManufacturingOrder;
use App\Models\Bom;
use App\Models\StockTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ManufacturingOrderController extends Controller
{
    // FORM CREATE (Inertia)
    public function create()
    {
        // Ambil BOM aktif beserta produk jadi-nya
        $boms = Bom::with('product')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Modul/Manufacturing/Create', [
            'boms' => $boms,
            'default_planned_date' => Carbon::today()->toDateString(),
        ]);
    }

    // SIMPAN MO BARU
    public function store(Request $request)
    {
        $data = $request->validate([
            'bom_id'          => 'required|exists:boms,id',
            'qty_to_produce'  => 'required|numeric|min:0.0001',
            'planned_date'    => 'required|date',
            'note'            => 'nullable|string',
        ]);

        $bom = Bom::with('product')->findOrFail($data['bom_id']);

        // Generate kode MO sederhana
        $nextNumber = (ManufacturingOrder::max('id') ?? 0) + 1;
        $code = 'MO-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $mo = ManufacturingOrder::create([
            'code'           => $code,
            'product_id'     => $bom->product_id, // produk jadi dari BOM
            'bom_id'         => $bom->id,
            'qty_to_produce' => $data['qty_to_produce'],
            'status'         => 'DRAFT',
            'planned_date'   => $data['planned_date'],
            'done_date'      => null,
            'note'           => $data['note'] ?? null,
        ]);

        return redirect()
            ->route('manufacturing')
            ->with('success', 'Manufacturing Order berhasil dibuat.');
    }

    // DETAIL MO (masih pakai Blade sesuai aslinya)
    public function show(ManufacturingOrder $manufacturingOrder)
    {
        $mo = $manufacturingOrder->load(['product', 'bom.items.product' => function ($query) {
            $query->withSum(['stockTransactions as total_in' => function ($q) {
                $q->where('trx_type', 'IN');
            }], 'qty')
            ->withSum(['stockTransactions as total_out' => function ($q) {
                $q->where('trx_type', 'OUT');
            }], 'qty');
        }]);

        // hitung kebutuhan bahan
        $requirements = $mo->bom->items->map(function ($item) use ($mo) {
            $product = $item->product;
            $currentStock = ($product->total_in ?? 0) - ($product->total_out ?? 0);

            return [
                'id'             => $item->id,
                'raw_product'    => $product,
                'qty_per_unit'   => $item->qty_per_unit,
                'total_required' => $item->qty_per_unit * $mo->qty_to_produce,
                'available_stock'=> $currentStock,
            ];
        })->values();

        return Inertia::render('Modul/Manufacturing/ManufacturingShow', [
            'mo'           => $mo,
            'requirements' => $requirements,
        ]);
    }

    public function complete(ManufacturingOrder $manufacturingOrder)
    {
        if ($manufacturingOrder->status === 'DONE') {
            return back()->with('error', 'MO sudah selesai.');
        }

        DB::transaction(function () use ($manufacturingOrder) {
            $mo = $manufacturingOrder->load('bom.items.product');

            $today = Carbon::today()->toDateString();
            $ref = $mo->code;
            $locationId = 1; // TODO: sesuaikan, saat ini hardcode

            // 1. OUT bahan baku
            foreach ($mo->bom->items as $item) {
                $totalRequired = $item->qty_per_unit * $mo->qty_to_produce;

                StockTransaction::create([
                    'product_id'  => $item->product_id,
                    'location_id' => $locationId,
                    'trx_type'    => 'OUT',
                    'qty'         => $totalRequired,
                    'trx_date'    => $today,
                    'reference'   => $ref,
                    'note'        => 'Consumption for ' . $mo->code,
                ]);
            }

            // 2. IN barang jadi
            StockTransaction::create([
                'product_id'  => $mo->product_id,
                'location_id' => $locationId,
                'trx_type'    => 'IN',
                'qty'         => $mo->qty_to_produce,
                'trx_date'    => $today,
                'reference'   => $ref,
                'note'        => 'Production for ' . $mo->code,
            ]);

            // 3. update status MO
            $mo->update([
                'status'    => 'DONE',
                'done_date' => $today,
            ]);
        });

        return back()->with('success', 'MO berhasil diselesaikan dan stok terupdate.');
    }
}
