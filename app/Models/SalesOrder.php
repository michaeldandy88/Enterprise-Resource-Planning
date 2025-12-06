<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SalesOrderItem;
use App\Models\Customer;

class SalesOrder extends Model
{
    protected $fillable = [
        'so_number', 'order_date', 'customer_id', 'status', 'total_amount'
    ];

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
