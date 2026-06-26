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
        'loa_authors',
        'loa_volume_id',
        'loa_approved_at',
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

    protected $dates = ['deleted_at', 'loa_approved_at'];

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

    public function loaVolume(): BelongsTo
    {
        return $this->belongsTo(LoaVolume::class, 'loa_volume_id');
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
}
