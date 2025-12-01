<?php

// app/Models/ManufacturingOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingOrder extends Model
{
    protected $fillable = [
        'code',
        'product_id',
        'bom_id',
        'qty_to_produce',
        'status',
        'planned_date',
        'done_date',
        'note',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'done_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }
}

