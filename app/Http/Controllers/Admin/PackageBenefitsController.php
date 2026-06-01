<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageBenefit;
use App\Models\Package;
use App\Models\MembershipBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PackageBenefitsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'membership_benefit_id' => 'required|exists:membership_benefits,id',
            'value_type' => 'nullable|string|max:50',
            'value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'quota' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        // Prevent duplicate
        $exists = PackageBenefit::where('package_id', $validated['package_id'])
            ->where('membership_benefit_id', $validated['membership_benefit_id'])
            ->exists();

        if ($exists) {
            return Redirect::back()->with('error', 'Benefit already assigned to package.');
        }

        PackageBenefit::create($validated);

        return Redirect::back()->with('success', 'Benefit assigned to package.');
    }

    public function destroy(PackageBenefit $packageBenefit)
    {
        $packageBenefit->delete();

        return Redirect::back()->with('success', 'Package benefit removed.');
    }
}
