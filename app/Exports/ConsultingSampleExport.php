<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConsultingSampleExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new Sheets\ConsultingsSheet(),
            new Sheets\ClientsSheet(),
            new Sheets\ObjectivesSheet(),
            new Sheets\ExpertiseSheet(),
            new Sheets\FocusAreasSheet(),
        ];
    }
}
