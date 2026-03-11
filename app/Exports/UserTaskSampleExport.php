<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserTaskSampleExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new Sheets\UserTasksSheet(),
            new Sheets\StaffSheet(),
            new Sheets\ClientsSheet(),
            new Sheets\PrioritiesSheet(),
            new Sheets\StatusesSheet(),
        ];
    }
}