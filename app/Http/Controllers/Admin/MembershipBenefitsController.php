<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Inertia\Response;

class MembershipBenefitsController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 15);

        $query = MembershipBenefit::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
        }

        $benefits = $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->all());

        return Inertia::render('Admin/MembershipBenefits/Index', [
            'membershipBenefits' => $benefits,
            'filters' => RequestFacade::all('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/MembershipBenefits/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64|unique:membership_benefits,code',
            'name' => 'required|string|max:255',
            'benefit_type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        MembershipBenefit::create($validated);

        return Redirect::route('membership-benefits.index')->with('success', 'Benefit created.');
    }

    public function edit(MembershipBenefit $membership_benefit): Response
    {
        return Inertia::render('Admin/MembershipBenefits/Edit', [
            'membershipBenefit' => $membership_benefit,
        ]);
    }

    public function update(MembershipBenefit $membership_benefit, Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64|unique:membership_benefits,code,' . $membership_benefit->id,
            'name' => 'required|string|max:255',
            'benefit_type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $membership_benefit->update($validated);

        return Redirect::route('membership-benefits.index')->with('success', 'Benefit updated.');
    }

    public function destroy(MembershipBenefit $membership_benefit)
    {
        $membership_benefit->delete();

        return Redirect::back()->with('success', 'Benefit deleted.');
    }
}
