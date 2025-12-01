<?php

// app/Http/Controllers/ManufacturingOrderController.php
namespace App\Http\Controllers;

use App\Models\ManufacturingOrder;
use App\Models\StockTransaction;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturingOrderController extends Controller
{
    public function show(ManufacturingOrder $manufacturingOrder)
    {
        $mo = $manufacturingOrder->load('product', 'bom.items.product');

        // hitung kebutuhan bahan
        $requirements = $mo->bom->items->map(function ($item) use ($mo) {
            return [
                'raw_product' => $item->product,
                'qty_per_unit' => $item->qty_per_unit,
                'total_required' => $item->qty_per_unit * $mo->qty_to_produce,
            ];
        });

        return view('manufacturing_orders.show', compact('mo', 'requirements'));
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
            $locationId = 1; // TODO: hardcode dulu ke lokasi Gudang Utama (sesuaikan)

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
                'status'   => 'DONE',
                'done_date'=> $today,
            ]);
        });

        return back()->with('success', 'MO berhasil diselesaikan dan stok terupdate.');
    }
}

