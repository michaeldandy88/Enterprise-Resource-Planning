<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer; // ← WAJIB ADA

class CustomerSeeder extends Seeder
{
    public function run()
    {
        Customer::create([
            'customer_code' => 'CUST001',
            'name'          => 'PT Sumber Makmur',
            'email'         => 'contact@sumbermakmur.com',
            'phone'         => '081234567890',
            'address'       => 'Jl. Raya No. 1',
            'city'          => 'Surabaya',
            'country'       => 'Indonesia',
        ]);
    }
}
