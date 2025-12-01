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
    public function index()
    {
        $mos = ManufacturingOrder::with('product', 'bom')
            ->orderByDesc('id')
            ->paginate(10);

        return view('manufacturing_orders.index', compact('mos'));
    }

    public function create()
    {
        // Ambil produk FINISHED + BOM buat dipilih
        $boms = \App\Models\Bom::with('product')->get();

        return view('manufacturing_orders.create', compact('boms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:manufacturing_orders,code',
            'bom_id'         => 'required|exists:boms,id',
            'qty_to_produce' => 'required|numeric|min:0.001',
            'planned_date'   => 'nullable|date',
            'note'           => 'nullable|string',
        ]);

        $bom = \App\Models\Bom::with('product')->findOrFail($data['bom_id']);

        $data['product_id'] = $bom->product_id;
        $data['status']     = 'DRAFT';

        ManufacturingOrder::create($data);

        return redirect()->route('manufacturing-orders.index')->with('success', 'MO created.');
    }

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

