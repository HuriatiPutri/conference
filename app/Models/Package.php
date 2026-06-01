<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price_idr',
        'price_usd',
        'status',
        'duration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price_idr' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'duration'  => 'integer',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function packageBenefits()
    {
        return $this->hasMany(PackageBenefit::class);
    }

    public function membershipBenefits()
    {
        return $this->belongsToMany(MembershipBenefit::class, 'package_benefits')
            ->withPivot([
                'id',
                'value_type',
                'value',
                'max_value',
                'quota',
                'notes',
            ])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
