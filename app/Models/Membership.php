<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class Membership extends Model
{
    protected $fillable = [
        'public_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'institution',
        'phone_number',
        'country',
        'package_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Auto-generate public_id on create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->public_id)) {
                $model->public_id = 'MBR-' . strtoupper(Str::random(10));
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * InvoiceHistory records referencing this membership (polymorphic)
     */
    public function invoices()
    {
        return $this->morphMany(InvoiceHistory::class, 'reference');
    }

    public function audiences()
    {
        return $this->hasMany(Audience::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Activate this membership.
     * Sets start_date = today, end_date = today + package duration (days).
     */
    public function activate(): void
    {
        $duration = $this->package?->duration ?? 365;

        $this->update([
            'status' => 'active',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($duration)->toDateString(),
        ]);
    }

    /**
     * Send set-password email after payment is verified.
     */
    public function sendSetPasswordEmail(): void
    {
        // Generate a plain text token
        $token = Str::random(60);

        // Store hashed token in the password_reset_tokens table
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            [
                'email' => $this->email,
                'token' => \Illuminate\Support\Facades\Hash::make($token),
                'created_at' => now(),
            ]
        );

        $data = [
            'name' => $this->first_name . ' ' . $this->last_name,
            'package_name' => $this->package?->name ?? 'Membership',
            'set_password_url' => url('/membership/set-password/' . $token . '?email=' . urlencode($this->email)),
        ];

        Mail::send('emails.membership_set_password', $data, function ($message) {
            $message->to($this->email, $this->first_name . ' ' . $this->last_name)
                ->subject('Welcome! Please Set Your Password');
        });
    }

    /**
     * Send payment pending email (for bank transfer).
     */
    public function sendPaymentPendingEmail(float $amount, string $currency): void
    {
        $data = [
            'name' => $this->first_name . ' ' . $this->last_name,
            'package_name' => $this->package?->name ?? 'Membership',
            'amount' => $amount,
            'currency' => $currency,
            'public_id' => $this->public_id,
        ];

        Mail::send('emails.membership_payment_pending', $data, function ($message) {
            $message->to($this->email, $this->first_name . ' ' . $this->last_name)
                ->subject('Membership Payment Received – Pending Verification');
        });
    }
}
