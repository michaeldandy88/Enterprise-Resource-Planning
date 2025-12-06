<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index()
    {
        $vendors = Supplier::orderBy('name')->paginate(10);

        return Inertia::render('Modul/Purchase/Vendor/Index', [
            'vendors' => $vendors
        ]);
    }

    public function create()
    {
        return Inertia::render('Modul/Purchase/Vendor/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Supplier $vendor)
    {
        return Inertia::render('Modul/Purchase/Vendor/Edit', [
            'vendor' => $vendor
        ]);
    }

    public function update(Request $request, Supplier $vendor)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $vendor->update($request->all());

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Supplier $vendor)
    {
        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
