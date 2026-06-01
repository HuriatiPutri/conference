<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageBenefit extends Model
{
    protected $fillable = [
        'package_id',
        'membership_benefit_id',
        'value_type',
        'value',
        'max_value',
        'quota',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'quota' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function membershipBenefit()
    {
        return $this->belongsTo(MembershipBenefit::class);
    }
}
