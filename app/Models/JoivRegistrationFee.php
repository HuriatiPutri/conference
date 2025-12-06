<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoivRegistrationFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'usd_amount',
        'idr_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'usd_amount' => 'decimal:2',
        'idr_amount' => 'decimal:2',
    ];

    /**
     * Get the user who created this fee
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the current active fee (latest one)
     */
    public static function getCurrentFee(): ?self
    {
        return self::orderBy('created_at', 'desc')->first();
    }

    /**
     * Get the current fee amount for a specific currency
     *
     * @param string $currency 'USD' or 'IDR'
     * @return float
     */
    public static function getCurrentFeeAmount(string $currency = 'USD'): float
    {
        $fee = self::getCurrentFee();
        
        if (!$fee) {
            // Default fallback values
            return $currency === 'IDR' ? 2250000.00 : 150.00;
        }
        
        return $currency === 'IDR' ? (float) $fee->idr_amount : (float) $fee->usd_amount;
    }

    /**
     * Get current USD amount
     */
    public static function getCurrentUsdAmount(): float
    {
        return self::getCurrentFeeAmount('USD');
    }

    /**
     * Get current IDR amount
     */
    public static function getCurrentIdrAmount(): float
    {
        return self::getCurrentFeeAmount('IDR');
    }
}
