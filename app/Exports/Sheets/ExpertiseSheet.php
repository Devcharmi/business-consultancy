<?php

namespace App\Exports\Sheets;

use App\Models\ExpertiseManager;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpertiseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return ExpertiseManager::activeExpertise()
            ->select(
                'name',
            )
            ->orderBy('id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'name',
        ];
    }

      public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Row 1 (Heading Row)
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'], // White text
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'argb' => 'FF1F4E78', // Dark Blue Background
                    ],
                ],
            ],
        ];
    }
    
    public function title(): string
    {
        return 'Expertise';
    }
}
