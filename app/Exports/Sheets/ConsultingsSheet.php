<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsultingsSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [

            // HEADER ROW
            [
                'client_name',
                'objective_name',
                'expertise_name',
                'focus_area_name',
                'consulting_date (YYYY-MM-DD)',
                'start_time (HH:MM)',
                'end_time (HH:MM)'
            ],

            // BLANK ROW FOR USER ENTRY
            [
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
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
        return 'Consultings';
    }
}
