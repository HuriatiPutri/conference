<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'loa_authors',
        'loa_volume_id',
        'voucher_id',
        'voucher_code',
        'loa_approved_at',
        'full_paper_path',
        'original_fee',
        'discount_amount',
        'payment_status',
        'payment_method',
        'payment_proof_path',
        'paid_fee',
        'currency',
        'public_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'original_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_fee' => 'decimal:2',
    ];

    protected $dates = ['deleted_at', 'loa_approved_at'];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(InvoiceHistory::class, 'reference');
    }

    public function invoiceHistories(): MorphMany
    {
        return $this->morphMany(InvoiceHistory::class, 'reference');
    }

    public function benefitUsages(): MorphMany
    {
        return $this->morphMany(BenefitUsage::class, 'reference');
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

    public function loaVolume(): BelongsTo
    {
        return $this->belongsTo(LoaVolume::class, 'loa_volume_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
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
                return '-';
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
            case 'expired':
                return 'Expired';
            default:
                return 'Status Tidak Diketahui';
        }
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Send registration confirmation email with payment link.
     */
    public function sendRegistrationEmail()
    {
        $data = [
            'name' => $this->first_name . ' ' . $this->last_name,
            'initial' => 'JOIV',
            'registration_number' => $this->public_id ?? 'REG-' . $this->id,
            'registration_date' => $this->created_at ? $this->created_at->format('d M Y') : now()->format('d M Y'),
            'paper_title' => $this->paper_title ?? 'Untitled Paper',
            'conference_name' => 'JOIV: International Journal on Informatics Visualization',
            'year' => now()->format('Y'),
            'place' => 'Online, International',
            'email' => $this->email_address,
            'phone_number' => $this->phone_number,
            'payment_link' => route('joiv.payment', ['registration' => $this->public_id]),
        ];

        \Illuminate\Support\Facades\Mail::send('emails.registration_confirmation', $data, function ($message) {
            $message->to($this->email_address, "{$this->first_name} {$this->last_name}")
                ->subject('Registration Confirmation – JOIV');
        });
    }

    /**
     * Send LoA email with PDF attachment.
     */
    public function sendLoaEmail()
    {
        $this->load(['loaVolume']);

        $data = [
            'name' => $this->first_name . ' ' . $this->last_name,
            'initial' => 'JOIV',
            'registration_number' => $this->public_id ?? 'REG-' . $this->id,
            'paper_title' => $this->paper_title ?? 'Untitled Paper',
            'authors' => $this->loa_authors,
            'joiv_volume' => $this->loaVolume->volume ?? 'Volume Not Set',
            'conference_name' => 'Journal on Informatics Visualization',
            'year' => now()->format('Y'),
            'place' => 'Online, International',
            'email' => $this->email_address,
        ];

        \Illuminate\Support\Facades\Mail::send('emails.loa_email', $data, function ($message) {
            $message->to($this->email_address, "{$this->first_name} {$this->last_name}")
                ->subject("Letter of Acceptance (LoA) – JOIV");

            try {
                $loaPdf = $this->generateLoaPdfContent();
                if ($loaPdf) {
                    $fileName = "JOIV-Acceptance-Letter-{$this->first_name}-{$this->last_name}.pdf";
                    $message->attachData($loaPdf, $fileName, [
                        'mime' => 'application/pdf',
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error attaching JOIV LoA PDF to email: ' . $e->getMessage());
            }
        });
    }

    /**
     * Generate LoA PDF content.
     */
    public function generateLoaPdfContent()
    {
        try {
            $this->load(['loaVolume']);

            $data = [
                'participant_name' => $this->first_name . ' ' . $this->last_name,
                'institution' => $this->institution ?? 'Unknown Institution',
                'paper_title' => $this->paper_title ?? 'Untitled Paper',
                'authors' => $this->loa_authors,
                'joiv_volume' => $this->loaVolume->volume ?? 'Volume Not Set',
                'conference_name' => 'Journal on Informatics Visualization',
                'conference_initial' => 'JOIV',
                'conference_date' => now(),
                'conference_city' => 'Online',
                'conference_country' => 'International',
                'presentation_type' => 'journal article',
                'registration_number' => $this->public_id ?? 'REG-' . $this->id,
                'number_of_letter' => 'No: SOTVI/LoA/' . date('Y') . '/' . ($this->public_id),
                'issue_date' => $this->loa_approved_at ? \Carbon\Carbon::parse($this->loa_approved_at)->format('d F Y') : now()->format('d F Y'),
                'signature_path' => storage_path('app/public/images/loa_signature.png'),
                'joiv_logo_path' => storage_path('app/public/images/joiv_logo.png'),
                'sotvi_logo_path' => storage_path('app/public/images/sotvi_logo.png'),
                'scopus_analitic_path' => storage_path('app/public/images/scopus.png'),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('letters-of-approval.template-clean', compact('data'))
                ->setPaper('A4', 'portrait');

            return $pdf->output();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating JOIV LoA PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send payment confirmation/status update email with optional receipt.
     */
    public function sendPaymentConfirmationEmail()
    {
        $amountFormatted = $this->country === 'ID'
            ? number_format($this->paid_fee, 0, ',', '.')
            : number_format($this->paid_fee, 2);

        $data = [
            'name' => $this->first_name . ' ' . $this->last_name,
            'initial' => 'JOIV',
            'registration_number' => $this->public_id ?? 'REG-' . $this->id,
            'registration_date' => $this->created_at ? $this->created_at->format('d M Y') : now()->format('d M Y'),
            'paper_title' => $this->paper_title ?? 'Untitled Paper',
            'conference_name' => 'JOIV: International Journal on Informatics Visualization',
            'year' => now()->format('Y'),
            'place' => 'Online, International',
            'email' => $this->email_address,
            'phone_number' => $this->phone_number,
            'amount' => $amountFormatted,
            'payment_date' => $this->updated_at ? $this->updated_at->format('d M Y H:i:s') : now()->format('d M Y H:i:s'),
            'payment_method' => $this->getPaymentMethodText(),
            'payment_status' => $this->getPaymentStatusText(),
        ];

        $template = [
            'paid' => 'emails.payment_confirmation',
            'cancelled' => 'emails.payment_cancelled',
            'refunded' => 'emails.payment_refunded',
            'pending_payment' => 'emails.payment_pending',
            'expired' => 'emails.payment_expired',
        ];

        if (!isset($template[$this->payment_status])) {
            return;
        }

        \Illuminate\Support\Facades\Mail::send($template[$this->payment_status], $data, function ($message) {
            $message->to($this->email_address, "{$this->first_name} {$this->last_name}")
                ->subject("Payment Confirmation – JOIV");

            // Attach receipt PDF jika status adalah 'paid'
            if ($this->payment_status === 'paid') {
                try {
                    $receiptPdf = $this->generateReceiptPdfContent();
                    if ($receiptPdf) {
                        $fileName = "receipt-{$this->first_name}-{$this->last_name}.pdf";
                        $message->attachData($receiptPdf, $fileName, [
                            'mime' => 'application/pdf',
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error attaching JOIV receipt PDF: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Generate Receipt PDF content.
     */
    public function generateReceiptPdfContent()
    {
        try {
            $data = [
                'name' => $this->first_name . ' ' . $this->last_name,
                'address' => $this->institution . ', ' . $this->country,
                'paper_title' => $this->paper_title ?? 'N/A',
                'conference' => 'JOIV',
                'conference_name' => 'JOIV: International Journal on Informatics Visualization',
                'conference_cover' => null,
                'date' => now()->format('Y'),
                'amount' => $this->country === 'ID' ? 'Rp' . number_format($this->paid_fee, 0, ',', '.') : '$' . number_format($this->paid_fee, 2),
                'payment_method' => $this->payment_method === 'transfer_bank' ? 'Bank Transfer' : 'Payment Gateway',
                'payment_date' => $this->updated_at ? $this->updated_at->format('d M Y H:i') : now()->format('d M Y H:i'),
                'invoice_id' => 'Ref. No.' . strtoupper($this->public_id) . '/PAID/JOIV/' . now()->format('Y'),
                'signature' => storage_path('app/public/images/joiv-signature.png'),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipt.joiv', compact('data'))
                ->setPaper('A4', 'portrait');

            return $pdf->output();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating JOIV Receipt PDF: ' . $e->getMessage());
            return null;
        }
    }
}
