<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipBenefit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'benefit_type',
        'description',
    ];

    public function packageBenefits()
    {
        return $this->hasMany(PackageBenefit::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_benefits')
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
}
