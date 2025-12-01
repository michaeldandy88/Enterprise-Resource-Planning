<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    protected $fillable = ['product_id', 'name', 'is_active'];

    public function product()
    {
        return $this->belongsTo(Product::class); // product jadi
    }

    public function items()
    {
        return $this->hasMany(BomItem::class);
    }
}

