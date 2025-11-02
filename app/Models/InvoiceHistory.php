<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceHistory extends Model
{
    use HasFactory;

    protected $table = 'invoice_history';
    
    // Override primary key type since it might be varchar
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'public_id',
        'audience_id',
        'conference_id',
        'payment_gateway',
        'payment_method',
        'transaction_id',
        'payer_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'execution_response',
        'description',
        'return_url',
        'cancel_url',
        'payment_initiated_at',
        'payment_completed_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'execution_response' => 'array',
        'amount' => 'decimal:2',
        'payment_initiated_at' => 'datetime',
        'payment_completed_at' => 'datetime',
    ];

    /**
     * Generate unique public ID for the invoice
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Generate unique string ID
            if (empty($model->id)) {
                $model->id = 'IH-' . strtoupper(uniqid());
            }
            
            if (empty($model->public_id)) {
                $model->public_id = 'INV-' . strtoupper(uniqid());
            }
            
            if (empty($model->invoice_number)) {
                $model->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1, 
                    4, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Relationship with Audience
     */
    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class);
    }

    /**
     * Relationship with Conference
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Scope for successful payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for PayPal payments
     */
    public function scopePayPal($query)
    {
        return $query->where('payment_gateway', 'paypal');
    }

    public function sendEmail()
    {
        $data = [
            'audience_id' => $this->audience_id,
            'initial' => $this->audience->conference->initial,
            'conference_name' => $this->audience->conference->name,
            'year' => $this->audience->conference->year,
            'place' => $this->audience->conference->city.', '.$this->audience->conference->country,
            'name' => $this->audience->first_name.' '.$this->audience->last_name,
            'registration_number' => $this->audience->id,
            'registration_date' => $this->audience->created_at->format('d M Y'),
            'snap_token' => $this->snap_token,
            'expired_at' => $this->expired_at,
            'redirect_url' => $this->redirect_url,
            'payment_date' => now()->format('d M Y H:i:s'),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'payment_link' => route('registration.show', ['audience_id' => $this->audience_id]),
        ];

        $template = [
            'paid' => 'emails.payment_success',
            'settlement' => 'emails.payment_success',
            'pending' => 'emails.payment_pending',
            'deny' => 'emails.payment_failed',
            'expire' => 'emails.payment_expired',
            'cancel' => 'emails.payment_cancelled',
        ];

        Mail::send($template[$this->status], $data, function ($message) {
            $message->to($this->audience->email)
                    ->subject('Payment Confirmation – '.$this->audience->conference->initial);

            // Attach receipt PDF jika status adalah 'paid'
            if ($this->status === 'paid') {
                try {
                    $receiptPdf = $this->audience->downloadReceiptFromUrl();
                    if ($receiptPdf) {
                        $fileName = "receipt-{$this->audience->first_name}-{$this->audience->last_name}.pdf";
                        $message->attachData($receiptPdf, $fileName, [
                            'mime' => 'application/pdf',
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error attaching receipt PDF: ' . $e->getMessage());
                }
            }
        });
    }
}