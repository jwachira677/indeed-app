<?php

namespace App\Http\Controllers;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Controllers\Concerns\AuthorizesCrud;

class TransportRouteController extends Controller implements HasMiddleware
{
    use AuthorizesCrud;
    protected static string $permissionModule = 'transport_routes';
    public function index()
    {
        try {
            $routes = TransportRoute::latest()->paginate(15);
            return view('transport.routes.index', compact('routes'));
        } catch (\Exception $e) {
            Log::error('Error fetching transport routes: ' . $e->getMessage());
            return back()->with('error', 'Failed to load transport routes.');
        }
    }

    public function create()
    {
        return view('transport.routes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100', 'unique:transport_routes,name'],
            'amount'    => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            TransportRoute::create([
                'name'      => $validated['name'],
                'amount'    => $validated['amount'],
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('transport-routes.index')->with('success', 'Transport Route created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating transport route: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create transport route.');
        }
    }

    public function edit(TransportRoute $transportRoute)
    {
        return view('transport.routes.form', compact('transportRoute'));
    }

    public function update(Request $request, TransportRoute $transportRoute)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100', 'unique:transport_routes,name,' . $transportRoute->id],
            'amount'    => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $transportRoute->update([
                'name'      => $validated['name'],
                'amount'    => $validated['amount'],
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('transport-routes.index')->with('success', 'Transport Route updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating transport route: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update transport route.');
        }
    }

    public function destroy(TransportRoute $transportRoute)
    {
        try {
            // Safety Check: Prevent deletion if students are currently using this route
            if ($transportRoute->students()->exists()) {
                return redirect()->route('transport-routes.index')
                    ->with('error', 'Cannot delete this route. There are students currently assigned to it.');
            }

            $transportRoute->delete();
            return redirect()->route('transport-routes.index')->with('success', 'Transport Route deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting transport route: ' . $e->getMessage());
            return redirect()->route('transport-routes.index')->with('error', 'Failed to delete transport route.');
        }
    }
}