<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->paginate(10);

        return Inertia::render('Modul/Employee/Index', [
            'employees' => $employees,
        ]);
    }

    public function create()
    {
        return Inertia::render('Modul/Employee/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:50|unique:employees,code',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
            'notes' => 'nullable|string',
        ]);

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created.');
    }

    public function show(Employee $employee)
    {
        return Inertia::render('Modul/Employee/Show', [
            'employee' => $employee,
        ]);
    }
    
    public function edit(Employee $employee)
    {
        return Inertia::render('Modul/Employee/Edit', [
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:50|unique:employees,code,' . $employee->id,
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
            'notes' => 'nullable|string',
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }
}
