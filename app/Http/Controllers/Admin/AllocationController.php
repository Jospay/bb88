<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PercentageType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AllocationController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard/Allocation', [
            // Ensure this key matches what your Vue file uses in defineProps
            'allocation' => PercentageType::all()
        ]);
    }

    public function store(Request $request)
    {
        $currentTotal = PercentageType::sum('value');
        $remaining = 100 - $currentTotal;

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:percentage_types,name',
            'value' => "required|numeric|min:0|max:$remaining",
        ], [
            'value.max' => "Total cannot exceed 100%. Only $remaining% remaining."
        ]);

        // Forced to 'Percentage' to match your enum in migration
        $validated['type'] = 'Percentage';

        PercentageType::create($validated);
        return redirect()->back();
    }

    // Change $id to PercentageType $allocation to match route {allocation}
    public function update(Request $request, PercentageType $allocation)
    {
        $otherTotal = PercentageType::where('id', '!=', $allocation->id)->sum('value');
        $remaining = 100 - $otherTotal;

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:percentage_types,name,' . $allocation->id,
            'value' => "required|numeric|min:0|max:$remaining",
        ], [
            'value.max' => "Total cannot exceed 100%. Max allowed for this item is $remaining%."
        ]);

        $allocation->update($validated);
        return redirect()->back();
    }

    public function destroy(PercentageType $allocation)
    {
        $allocation->delete();
        return redirect()->back();
    }
}
