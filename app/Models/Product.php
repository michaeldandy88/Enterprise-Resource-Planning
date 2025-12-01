<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code', 'name', 'uom', 'type', 'is_active',
    ];

    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}