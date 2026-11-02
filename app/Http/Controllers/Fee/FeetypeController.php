<?php

namespace App\Http\Controllers\Fee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeeType;
class FeetypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    try {
        $query = FeeType::query();

        // Optional: Search by name or code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Optional: Filter by active status
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        // Paginate the results (15 per page) and keep the search query in the URL
        $feeTypes = $query->latest()->paginate(15)->withQueryString();

        return view('fee.fee_types.index', compact('feeTypes'));

    } catch (\Exception $e) {
        \Log::error('Error fetching fee types: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to load fee types.');
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fee.fee_types.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FeeType $feeType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeeType $feeType)
    {
         return view('fee.fee_types.form', compact('feeType'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, FeeType $feeType)
    {
        $validated = $request->validate([
            'code'              => ['required', 'string', 'max:30', 'unique:fee_types,code,' . $feeType->id],
            'name'              => ['required', 'string', 'max:100'],
            'description'       => ['nullable', 'string'],
            'billing_frequency' => ['required', 'in:PER_TERM,PER_MONTH,PER_YEAR,ONE_OFF'],
            'is_optional'       => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        try {
            $feeType->update([
                'code'              => strtoupper($validated['code']),
                'name'              => $validated['name'],
                'description'       => $validated['description'] ?? null,
                'billing_frequency' => $validated['billing_frequency'],
                'is_optional'       => $request->boolean('is_optional'),
                'is_active'         => $request->boolean('is_active'),
            ]);

            return redirect()->route('fee-types.index')->with('success', 'Fee Type updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating fee type: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update fee type.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeType $feeType)
    {
        //
    }
}
