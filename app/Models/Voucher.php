<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'start_date',
        'end_date',
        'quota',
        'used_count',
        'applies_to',
        'discount_type',
        'discount_value',
        'discount_description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'applies_to' => 'array',
        'discount_value' => 'decimal:2',
    ];

    public function claims()
    {
        return $this->hasMany(VoucherClaim::class);
    }

    public function isValidFor(string $transactionType): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $today = now()->toDateString();
        if ($today < $this->start_date?->toDateString() || $today > $this->end_date?->toDateString()) {
            return false;
        }

        if ($this->used_count >= $this->quota) {
            return false;
        }

        return in_array($transactionType, $this->applies_to ?? [], true);
    }
}
