<?php
// app/Services/StockService.php
namespace App\Services;

use App\Models\StockTransaction;

class StockService
{
    public function getStockForProductAtLocation(int $productId, int $locationId): float
    {
        $in = StockTransaction::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->where('trx_type', 'IN')
            ->sum('qty');

        $out = StockTransaction::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->where('trx_type', 'OUT')
            ->sum('qty');

        return $in - $out;
    }

    public function getStockForProduct(int $productId): float
    {
        $in = StockTransaction::where('product_id', $productId)
            ->where('trx_type', 'IN')
            ->sum('qty');

        $out = StockTransaction::where('product_id', $productId)
            ->where('trx_type', 'OUT')
            ->sum('qty');

        return $in - $out;
    }
}
