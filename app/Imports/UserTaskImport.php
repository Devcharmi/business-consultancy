<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserTaskImport implements WithMultipleSheets
{
    protected $taskSheet;

    public function __construct()
    {
        $this->taskSheet = new UserTaskSheetImport();
    }

    public function sheets(): array
    {
        return [
            0 => $this->taskSheet
        ];
    }

    public function getErrors()
    {
        return $this->taskSheet->errors ?? [];
    }

    public function getImportedCount()
    {
        return $this->taskSheet->importedCount ?? 0;
    }
}
