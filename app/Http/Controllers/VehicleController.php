<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Controllers\Concerns\AuthorizesCrud;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'vehicles';

    public function index()
    {
        try {
            // Eager load the driver relationship
            $vehicles = Vehicle::with('driver')->latest()->paginate(15);
            return view('transport.vehicles.index', compact('vehicles'));
        } catch (\Exception $e) {
            Log::error('Error fetching vehicles: ' . $e->getMessage());
            return back()->with('error', 'Failed to load vehicles.');
        }
    }

    public function create()
    {
        // Fetch only users whose role slug is 'driver'
        $drivers = User::whereHas('role', function ($query) {
            $query->where('slug', 'driver');
        })->orderBy('name')->get();

        return view('transport.vehicles.form', compact('drivers'));
    }

    public function edit(Vehicle $vehicle)
    {
        // Fetch only users whose role slug is 'driver'
        $drivers = User::whereHas('role', function ($query) {
            $query->where('slug', 'driver');
        })->orderBy('name')->get();

        return view('transport.vehicles.form', compact('vehicle', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id'           => ['nullable', 'exists:users,id'],
            'registration_number' => ['required', 'string', 'max:20', 'unique:vehicles,registration_number'],
            'model'               => ['required', 'string', 'max:100'],
            'capacity'            => ['required', 'integer', 'min:1'],
            'status'              => ['required', 'in:ACTIVE,MAINTENANCE,INACTIVE'],
        ]);

        try {
            Vehicle::create($validated);
            return redirect()->route('vehicles.index')->with('success', 'Vehicle registered successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating vehicle: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to register vehicle.');
        }
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'driver_id'           => ['nullable', 'exists:users,id'],
            'registration_number' => ['required', 'string', 'max:20', 'unique:vehicles,registration_number,' . $vehicle->id],
            'model'               => ['required', 'string', 'max:100'],
            'capacity'            => ['required', 'integer', 'min:1'],
            'status'              => ['required', 'in:ACTIVE,MAINTENANCE,INACTIVE'],
        ]);

        try {
            $vehicle->update($validated);
            return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating vehicle: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update vehicle.');
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        try {
            $vehicle->delete();
            return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle: ' . $e->getMessage());
            return redirect()->route('vehicles.index')->with('error', 'Failed to delete vehicle.');
        }
    }
}