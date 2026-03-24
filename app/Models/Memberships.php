<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memberships extends Model
{
    public function invoices()
    {
        return $this->morphMany(InvoiceHistory::class, 'reference');
    }
}
