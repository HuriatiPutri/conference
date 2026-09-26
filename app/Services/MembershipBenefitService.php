<?php

namespace App\Services;

use App\Models\BenefitUsage;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Model;

class MembershipBenefitService
{
    public function resolveActiveMembershipByEmail(string $email): ?Membership
    {
        return Membership::query()
            ->with(['package.packageBenefits.membershipBenefit'])
            ->where('email', $email)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->latest('id')
            ->first();
    }

    public function calculateForFee(?Membership $membership, float $baseFee): array
    {
        $paidFee = (float) $baseFee;
        $discountAmount = 0.0;
        $appliedBenefits = [];

        if (! $membership || ! $membership->package) {
            return [
                'paid_fee' => $paidFee,
                'discount_amount' => 0.0,
                'applied_benefits' => [],
            ];
        }

        $membership->loadMissing('package.packageBenefits.membershipBenefit');

        $packageBenefits = $membership->package->packageBenefits
            ->sortBy('id')
            ->values();

        foreach ($packageBenefits as $packageBenefit) {
            $benefit = $packageBenefit->membershipBenefit;

            if (! $benefit) {
                continue;
            }

            if ($benefit->benefit_type === 'discount') {
                $appliedAmount = $this->calculateDiscountAmount($paidFee, (string) $packageBenefit->value_type, $packageBenefit->value);

                if ($appliedAmount > 0) {
                    $paidFee = max(0, $paidFee - $appliedAmount);
                    $discountAmount += $appliedAmount;

                    $appliedBenefits[] = [
                        'membership_benefit_id' => $benefit->id,
                        'package_benefit_id' => $packageBenefit->id,
                        'benefit_type' => $benefit->benefit_type,
                        'consumed_value' => $appliedAmount,
                    ];
                }

                continue;
            }

            if ($benefit->benefit_type === 'free_registration') {
                $quota = (int) ($packageBenefit->quota ?? 0);
                $used = $this->getConsumedQuota($membership->id, $benefit->id);

                if ($quota > 0 && $used < $quota && $paidFee > 0) {
                    $appliedBenefits[] = [
                        'membership_benefit_id' => $benefit->id,
                        'package_benefit_id' => $packageBenefit->id,
                        'benefit_type' => $benefit->benefit_type,
                        'consumed_value' => 1,
                    ];

                    $discountAmount += $paidFee;
                    $paidFee = 0.0;
                }

                continue;
            }

            if (in_array($benefit->benefit_type, ['souvenir', 'opportunity'], true)) {
                $appliedBenefits[] = [
                    'membership_benefit_id' => $benefit->id,
                    'package_benefit_id' => $packageBenefit->id,
                    'benefit_type' => $benefit->benefit_type,
                    'consumed_value' => 1,
                ];
            }
        }

        return [
            'paid_fee' => $paidFee,
            'discount_amount' => $discountAmount,
            'applied_benefits' => $appliedBenefits,
        ];
    }

    public function recordUsage(?Membership $membership, Model $reference, array $appliedBenefits): void
    {
        if (! $membership || empty($appliedBenefits)) {
            return;
        }

        foreach ($appliedBenefits as $appliedBenefit) {
            BenefitUsage::create([
                'user_id' => $membership->user_id,
                'membership_id' => $membership->id,
                'membership_benefit_id' => $appliedBenefit['membership_benefit_id'] ?? null,
                'package_benefit_id' => $appliedBenefit['package_benefit_id'] ?? null,
                'benefit_type' => $appliedBenefit['benefit_type'] ?? 'unknown',
                'reference_type' => $reference::class,
                'reference_id' => $reference->getKey(),
                'consumed_value' => $appliedBenefit['consumed_value'] ?? 0,
            ]);
        }
    }

    public function getConsumedQuota(int $membershipId, int $membershipBenefitId): float
    {
        return (float) BenefitUsage::where('membership_id', $membershipId)
            ->where('membership_benefit_id', $membershipBenefitId)
            ->sum('consumed_value');
    }

    private function calculateDiscountAmount(float $currentFee, string $valueType, mixed $value): float
    {
        if ($currentFee <= 0 || $value === null) {
            return 0.0;
        }

        return match ($valueType) {
            'percentage' => round($currentFee * ((float) $value / 100), 2),
            'fixed' => min($currentFee, round((float) $value, 2)),
            default => 0.0,
        };
    }
}