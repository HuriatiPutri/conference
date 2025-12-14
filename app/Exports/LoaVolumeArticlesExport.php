<?php

namespace App\Exports;

use App\Models\LoaVolume;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoaVolumeArticlesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $loaVolume;

    public function __construct(LoaVolume $loaVolume)
    {
        $this->loaVolume = $loaVolume;
    }

    public function collection()
    {
        // Load both audiences (conference) and JOIV registrations
        $this->loaVolume->load([
            'audiences' => function($query) {
                $query->with(['conference'])
                      ->where('payment_status', 'paid')
                      ->whereNotNull('paper_title')
                      ->select('id', 'first_name', 'last_name', 'paper_title', 'loa_authors', 'institution', 'conference_id', 'loa_volume_id', 'email', 'country')
                      ->orderBy('first_name');
            },
            'joivRegistrations' => function($query) {
                $query->where('payment_status', 'paid')
                      ->whereNotNull('paper_title')
                      ->select('id', 'first_name', 'last_name', 'paper_title', 'loa_authors', 'institution', 'loa_volume_id', 'email_address', 'country')
                      ->orderBy('first_name');
            }
        ]);

        $articles = collect();

        // Add conference audiences
        foreach ($this->loaVolume->audiences as $audience) {
            $articles->push([
                'type' => 'Conference',
                'data' => $audience,
                'conference_name' => $audience->conference->name ?? 'N/A',
            ]);
        }

        // Add JOIV registrations
        foreach ($this->loaVolume->joivRegistrations as $registration) {
            $articles->push([
                'type' => 'JOIV',
                'data' => $registration,
                'conference_name' => 'JOIV Article',
            ]);
        }

        return $articles;
    }

    public function headings(): array
    {
        return [
            'No',
            'Type',
            'Conference/Journal',
            'Author Name',
            'Email',
            'Institution',
            'Country',
            'Paper Title',
            'All Authors',
        ];
    }

    public function map($article): array
    {
        static $counter = 0;
        $counter++;

        $data = $article['data'];
        
        return [
            $counter,
            $article['type'],
            $article['conference_name'],
            $data->first_name . ' ' . $data->last_name,
            $article['type'] === 'JOIV' ? $data->email_address : $data->email,
            $data->institution,
            $data->country,
            $data->paper_title,
            $data->loa_authors ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
