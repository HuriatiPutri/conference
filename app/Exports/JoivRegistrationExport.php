<?php

namespace App\Exports;

use App\Models\JoivRegistration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JoivRegistrationExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = JoivRegistration::query();

        // Apply filters
        if (!empty($this->filters['country'])) {
            $query->where('country', $this->filters['country']);
        }

        if (!empty($this->filters['institution'])) {
            $query->where('institution', 'ILIKE', "%{$this->filters['institution']}%");
        }

        if (!empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        if (!empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('email_address', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('paper_title', 'ILIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Public ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Institution',
            'Country',
            'Paper ID',
            'Paper Title',
            'Payment Status',
            'Payment Method',
            'Paid Fee',
            'Created At',
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->id,
            $registration->public_id,
            $registration->first_name,
            $registration->last_name,
            $registration->email_address,
            $registration->phone_number,
            $registration->institution,
            $registration->country,
            $registration->paper_id ?? '-',
            $registration->paper_title,
            $registration->getPaymentStatusText(),
            $registration->getPaymentMethodText(),
            '$' . number_format($registration->paid_fee, 2),
            $registration->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
