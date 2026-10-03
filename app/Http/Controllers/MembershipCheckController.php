<?php

namespace App\Http\Controllers;

use App\Services\MembershipBenefitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipCheckController extends Controller
{
    public function check(Request $request, MembershipBenefitService $membershipBenefitService): JsonResponse
    {
        $email = $request->query('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'is_member' => false,
                'message' => 'Invalid email address',
            ]);
        }

        $membership = $membershipBenefitService->resolveActiveMembershipByEmail($email);

        if (!$membership) {
            return response()->json([
                'is_member' => false,
                'message' => 'Not an active member',
            ]);
        }

        $membership->loadMissing(['package.packageBenefits.membershipBenefit']);

        // Collect discount benefits
        $discountBenefits = [];
        $packageBenefits = $membership->package?->packageBenefits ?? collect();

        foreach ($packageBenefits as $pb) {
            $benefit = $pb->membershipBenefit;
            if (!$benefit) {
                continue;
            }

            if ($benefit->benefit_type === 'discount' || $benefit->benefit_type === 'free_registration') {
                $discountBenefits[] = [
                    'benefit_id' => $benefit->id,
                    'benefit_name' => $benefit->name,
                    'benefit_type' => $benefit->benefit_type,
                    'value_type' => $pb->value_type,
                    'value' => (float) $pb->value,
                ];
            }
        }

        return response()->json([
            'is_member' => true,
            'membership' => [
                'id' => $membership->id,
                'public_id' => $membership->public_id,
                'name' => $membership->first_name . ' ' . $membership->last_name,
                'email' => $membership->email,
                'package_name' => $membership->package?->name ?? 'Membership',
                'discount_benefits' => $discountBenefits,
            ],
            'message' => 'Active membership found',
        ]);
    }
}
