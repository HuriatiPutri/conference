<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoivRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email_address',
        'phone_number',
        'institution',
        'country',
        'paper_id',
        'paper_title',
        'full_paper_path',
        'payment_status',
        'payment_method',
        'payment_proof_path',
        'paid_fee',
        'currency',
        'public_id',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['deleted_at'];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoiceHistories(): HasMany
    {
        return $this->hasMany(InvoiceHistory::class, 'joiv_registration_id');
    }

    // Helper methods
    public function getPaymentMethodText()
    {
        switch ($this->payment_method) {
            case 'transfer_bank':
                return 'Bank Transfer';
            case 'payment_gateway':
                return 'Payment Gateway';
            default:
                return 'Metode Pembayaran Tidak Diketahui';
        }
    }

    public function getPaymentStatusText()
    {
        switch ($this->payment_status) {
            case 'pending_payment':
                return 'Pending Payment';
            case 'paid':
                return 'Paid';
            case 'cancelled':
                return 'Cancelled';
            case 'refunded':
                return 'Refunded';
            default:
                return 'Status Tidak Diketahui';
        }
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
