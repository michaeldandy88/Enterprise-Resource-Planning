<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }
}
