<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BenefitUsage extends Model
{
    protected $fillable = [
        'user_id',
        'membership_id',
        'membership_benefit_id',
        'package_benefit_id',
        'benefit_type',
        'reference_type',
        'reference_id',
        'consumed_value',
    ];

    protected $casts = [
        'consumed_value' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function membershipBenefit(): BelongsTo
    {
        return $this->belongsTo(MembershipBenefit::class);
    }

    public function packageBenefit(): BelongsTo
    {
        return $this->belongsTo(PackageBenefit::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}