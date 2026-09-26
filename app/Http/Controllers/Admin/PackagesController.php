<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Inertia\Response;

class PackagesController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 15);

        $query = Package::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('price_idr', 'LIKE', "%{$search}%");
            });
        }

        $packages = $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->all());
        $packages->getCollection()->load(['packageBenefits.membershipBenefit']);

        return Inertia::render('Admin/Packages/Index', [
            'packages' => $packages,
            'filters' => RequestFacade::all('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Packages/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_idr' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();

        Package::create($validated);

        return Redirect::route('packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package): Response
    {
        $package->load(['packageBenefits.membershipBenefit']);

        // provide available membership benefits for the package edit form
        $availableBenefits = \App\Models\MembershipBenefit::orderBy('name')->get();

        return Inertia::render('Admin/Packages/Edit', [
            'package' => $package,
            'availableMembershipBenefits' => $availableBenefits,
        ]);
    }

    public function update(Package $package, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_idr' => 'nullable|numeric|min:0',
            'price_usd' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
        ]);

        $validated['updated_by'] = Auth::id();

        $package->update($validated);

        return Redirect::route('packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return Redirect::back()->with('success', 'Package deleted.');
    }
}
