<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Audience extends Model
{
    use HasFactory;

    protected $table = 'audiences';

    protected $fillable = [
        'public_id',
        'conference_id',
        'first_name',
        'last_name',
        'paper_title',
        'institution',
        'email',
        'phone_number',
        'country',
        'presentation_type',
        'paid_fee',
        'payment_status', // Tambahkan ini
        'payment_method', // Tambahkan ini
        'payment_proof_path', // Tambahkan ini
        'full_paper_path',
    ];

    // Definisikan 'payment_status' sebagai enum di PHP
    protected $casts = [
        'payment_status' => 'string', // Atau bisa juga array untuk enum jika PHP >= 8.1
        'payment_method' => 'string',
    ];

    public function getPaymentMethodText()
    {
        switch ($this->payment_method) {
            case 'transfer_bank':
                return 'Transfer Bank';
            case 'payment_gateway':
                return 'Payment Gateway';
            default:
                return 'Metode Pembayaran Tidak Diketahui';
        }
    }

    public function getPresentationTypeText()
    {
        switch ($this->presentation_type) {
            case 'online_author':
                return 'Online (author/presenter)';
            case 'onsite':
                return 'Onsite';
            case 'participant_only':
                return 'Participant Only';
            default:
                return 'Jenis Peserta Tidak Diketahui';
        }
    }

    public function getPaymentStatusText()
    {
        switch ($this->payment_status) {
            case 'pending_payment':
                return '<span class="badge badge-warning">Pending Payment</span>';
            case 'paid':
                return '<span class="badge badge-success">Paid</span>';
            case 'failed':
                return '<span class="badge badge-danger">Failed</span>';
            case 'refunded':
                return '<span class="badge badge-info">Refunded</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Canceled</span>';
            default:
                return '<span class="badge badge-dark">Unknow</span>';
        }
    }

    // Relasi 'belongsTo' ke Conference
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function keynote()
    {
        return $this->hasOne(KeyNote::class);
    }

    public function parallelSession()
    {
        return $this->hasOne(ParallelSession::class);
    }

    public function sendEmail()
    {
        $data = [
            'name' => $this->first_name.' '.$this->last_name,
            'initial' => $this->conference->initial,
            'registration_number' => $this->public_id,
            'registration_date' => $this->created_at->format('d M Y'),
            'paper_title' => $this->paper_title,
            'conference_name' => $this->conference->name,
            'year' => $this->conference->year,
            'place' => $this->conference->city.', '.$this->conference->country,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'payment_link' => route('registration.show', ['audience_id' => $this->public_id]),
        ];

        Mail::send('emails.registration_confirmation', $data, function ($message) {
            $message->to($this->email, "{$this->first_name} {$this->last_name}")
                    ->subject('Registration Confirmation');
        });
    }

    public function sendPaymentConfirmationEmail()
    {
        $data = [
            'name' => $this->first_name.' '.$this->last_name,
            'initial' => $this->conference->initial,
            'registration_number' => $this->public_id,
            'registration_date' => $this->created_at->format('d M Y'),
            'paper_title' => $this->paper_title,
            'conference_name' => $this->conference->name,
            'year' => $this->conference->year,
            'place' => $this->conference->city.', '.$this->conference->country,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'amount' => $this->paid_fee,
            'payment_method' => $this->getPaymentMethodText(),
            'payment_status' => $this->getPaymentStatusText(),
        ];

        $template = [
            'paid' => 'emails.payment_confirmation',
            'cancelled' => 'emails.payment_cancelled',
            'refunded' => 'emails.payment_refunded',
        ];

        Mail::send($template[$this->payment_status], $data, function ($message) {
            $message->to($this->email, "{$this->first_name} {$this->last_name}")
                    ->subject('Payment Confirmation');
        });
    }
}
