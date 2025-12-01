<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}

