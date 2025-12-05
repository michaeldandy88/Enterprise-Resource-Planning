<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Modul/Customer/Index', [
            'customers' => $customers
        ]);
    }

    public function create()
    {
        return Inertia::render('Modul/Customer/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_code' => 'required|unique:customers,customer_code',
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'country'       => 'nullable|string',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Modul/Customer/Edit', [
            'customer' => $customer
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_code' => 'required|unique:customers,customer_code,' . $customer->id,
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'country'       => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
