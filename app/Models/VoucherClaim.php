<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherClaim extends Model
{
    protected $fillable = [
        'voucher_id',
        'email',
        'transaction_type',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
