<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BenefitUsage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BenefitUsageController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 15);

        $query = BenefitUsage::query()
            ->with([
                'user',
                'membership.package',
                'membershipBenefit',
                'packageBenefit.package',
                'reference',
            ]);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search) {
                $builder->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('membership', function ($membershipQuery) use ($search) {
                    $membershipQuery->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhereHas('package', function ($packageQuery) use ($search) {
                            $packageQuery->where('name', 'LIKE', "%{$search}%");
                        });
                })
                ->orWhereHas('membershipBenefit', function ($benefitQuery) use ($search) {
                    $benefitQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhere('benefit_type', 'LIKE', "%{$search}%");
                })
                ->orWhere('reference_type', 'LIKE', "%{$search}%")
                ->orWhere('reference_id', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('benefit_type')) {
            $query->where('benefit_type', $request->string('benefit_type')->toString());
        }

        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->string('reference_type')->toString());
        }

        $benefitUsages = $query
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());

        return Inertia::render('Admin/BenefitUsages/Index', [
            'benefitUsages' => $benefitUsages,
            'filters' => $request->only(['search', 'benefit_type', 'reference_type']),
        ]);
    }
}