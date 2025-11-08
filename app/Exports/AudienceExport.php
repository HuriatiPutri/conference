<?php

namespace App\Exports;

use App\Models\Audience;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Database\Eloquent\Builder;

class AudienceExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Audience::query()
            ->with(['conference', 'key_notes', 'parallel_sessions'])
            ->whereHas('conference');

        // Apply filters
        if (!empty($this->filters['conference_id'])) {
            $query->where('conference_id', $this->filters['conference_id']);
        }

        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if (!empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('institution', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('paper_title', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('conference', function($confQuery) use ($searchTerm) {
                      $confQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        return $query->orderBy('id', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Conference',
            'First Name',
            'Last Name',
            'Email',
            'Institution',
            'Paper Title',
            'Country',
            'Phone',
            'Presentation Type',
            'Payment Method',
            'Payment Status',
            'Registration Date',
            'Keynote Sessions',
            'Parallel Sessions',
            'Has Certificate Template',
        ];
    }

    public function map($audience): array
    {
        return [
            $audience->id,
            $audience->conference->name ?? '',
            $audience->first_name,
            $audience->last_name,
            $audience->email,
            $audience->institution,
            $audience->paper_title,
            $audience->country,
            $audience->phone,
            $this->getPresentationType($audience->presentation_type),
            $this->getPaymentMethod($audience->payment_method),
            $this->getPaymentStatus($audience->payment_status),
            $audience->created_at->format('Y-m-d H:i:s'),
            $audience->key_notes->count(),
            $audience->parallel_sessions->count(),
            $this->hasCertificateTemplate($audience) ? 'Yes' : 'No',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    private function getPresentationType($type)
    {
        $types = [
            'presenter' => 'Presenter',
            'participant' => 'Participant',
        ];
        return $types[$type] ?? $type;
    }

    private function getPaymentMethod($method)
    {
        $methods = [
            'paypal' => 'PayPal',
            'transfer_bank' => 'Bank Transfer',
        ];
        return $methods[$method] ?? $method;
    }

    private function getPaymentStatus($status)
    {
        $statuses = [
            'pending_payment' => 'Pending Payment',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];
        return $statuses[$status] ?? $status;
    }

    private function hasCertificateTemplate($audience)
    {
        return $audience->conference && 
               $audience->conference->certificate_template_path && 
               $audience->conference->certificate_template_position &&
               $audience->key_notes->count() > 0 && 
               $audience->parallel_sessions->count() > 0;
    }
}
