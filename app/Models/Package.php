<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'status',
        'duration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'duration' => 'integer',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
