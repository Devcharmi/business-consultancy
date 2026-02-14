<?php

namespace App\Exports\Sheets;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Client::activeClients()
            ->select(
                'client_name',
                'email',
                'phone',
            )
            ->orderBy('id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'client_name',
            'email',
            'phone',
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
        return 'Clients';
    }
}
