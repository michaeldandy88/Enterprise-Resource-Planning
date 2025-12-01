<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('code')->paginate(10);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:locations,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Location::create($data);

        return redirect()->route('locations.index')->with('success', 'Location created.');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:locations,code,' . $location->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $location->update($data);

        return redirect()->route('locations.index')->with('success', 'Location updated.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted.');
    }
}
