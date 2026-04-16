<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'institution',
        'phone_number',
        'country',
        'package_id',
        'start_date',
        'end_date'
    ];

    public function invoices()
    {
        return $this->morphMany(InvoiceHistory::class, 'reference');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
