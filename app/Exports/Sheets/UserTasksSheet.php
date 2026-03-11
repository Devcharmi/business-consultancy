<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTasksSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Follow Up Call',
                'John Doe',
                'ABC Pvt Ltd',
                // '',
                'High',
                'Pending',
                // 'client',
                'task',
                // 'commitment',
                '2026-03-10',
                '2026-03-12',
                '',
                'Call client to discuss proposal',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'task_name',
            'staff_name',
            'client_name',
            // 'lead_name',
            'priority',
            'status',
            // 'entity_type',
            'task_type',
            // 'source_type',
            'task_start_date',
            'task_due_date',
            'task_end_date',
            'description',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'argb' => 'FF1F4E78',
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Tasks';
    }
}