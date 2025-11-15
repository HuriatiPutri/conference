<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Audience extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
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
        'payment_status',
        'payment_method',
        'payment_proof_path',
        'full_paper_path',
        'participant_type',
        'public_id',
        'loa_status',
        'loa_notes',
        'loa_approved_at',
        'loa_authors',
        'loa_volume_id',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function key_notes(): HasMany
    {
        return $this->hasMany(KeyNote::class);
    }

    public function parallel_sessions(): HasMany
    {
        return $this->hasMany(ParallelSession::class);
    }

    public function invoice_histories(): HasMany
    {
        return $this->hasMany(InvoiceHistory::class);
    }

    public function loaVolume()
    {
        return $this->belongsTo(LoaVolume::class, 'loa_volume_id');
    }

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

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
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
            'payment_link' => route('registration.details', ['conference' => $this->conference->public_id, 'audience' => $this->public_id]),
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
            
            // Attach receipt PDF jika status adalah 'paid'
            if ($this->payment_status === 'paid') {
                try {
                    $receiptPdf = $this->downloadReceiptFromUrl();
                    if ($receiptPdf) {
                        $fileName = "receipt-{$this->first_name}-{$this->last_name}.pdf";
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

    /**
     * Download receipt PDF from controller URL
     */
    private function downloadReceiptFromUrl()
    {
        try {
            // Ambil data yang sama seperti di controller
            $conference = $this->conference;
            
            if (!$conference) {
                Log::warning('No conference found for audience ID: ' . $this->id);
                return null;
            }

            if ($this->payment_status !== 'paid') {
                Log::warning('Payment not completed for audience ID: ' . $this->id);
                return null;
            }

            // Generate data yang sama seperti di controller
            $data = [
                'name' => $this->first_name.' '.$this->last_name,
                'address' => $this->institution. ', '.$this->country,
                'paper_title' => $this->paper_title ?? 'N/A',
                'conference' => $conference->initial,
                'conference_name' => $conference->name,
                'conference_cover' => $conference->cover_poster_path ? storage_path('app/public/'.$conference->cover_poster_path) : null,
                'date' => $conference->date,
                'amount' => $this->country === 'ID' ?  'Rp'.number_format($this->paid_fee, 2) : '$'.number_format($this->paid_fee, 2),
                'payment_method' => $this->payment_method,
                'payment_date' => $this->updated_at->format('d M Y H:i'),
                'invoice_id' => 'Ref. No.'.strtoupper($this->public_id).'/PAID/'.strtoupper($this->conference->initial).'/2025',
                'signature' => storage_path('app/public/images/signature.png'),
            ];

            // Generate PDF menggunakan DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipt.index', compact('data'))
                      ->setPaper('A4', 'portrait');

            return $pdf->output();
        } catch (\Exception $e) {
            Log::error('Error downloading receipt from URL: ' . $e->getMessage());
            return null;
        }
    }
}
