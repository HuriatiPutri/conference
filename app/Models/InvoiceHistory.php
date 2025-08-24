<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class InvoiceHistory extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_history'; // Secara default Laravel akan mencari 'conferences', tapi eksplisit lebih baik

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'audience_id',
        'snap_token',
        'expired_at', // Tambahkan ini untuk menyimpan waktu kedaluwarsa token
        'redirect_url', // Tambahkan ini untuk menyimpan URL redirect
        'payment_method', // Tambahkan ini untuk menyimpan metode pembayaran
        'amount',
        'status',
    ];

    // Jika ada relasi dengan tabel Audience, bisa didefinisikan di sini
    public function audience()
    {
        return $this->belongsTo(Audience::class);
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

        Mail::send($template[$this->payment_status], $data, function ($message) {
            $message->to($this->audience->email)
                    ->subject('Payment Confirmation – '.$this->audience->conference->initial);
        });
    }
}
