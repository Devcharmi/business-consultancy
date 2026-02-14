<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConsultingImport implements WithMultipleSheets
{
    protected $sheetImport;

    public function __construct()
    {
        $this->sheetImport = new ConsultingSheetImport();
    }

    public function sheets(): array
    {
        return [
            0 => $this->sheetImport, // ONLY first sheet
        ];
    }

    public function getErrors()
    {
        return $this->sheetImport->errors;
    }

    public function getImportedCount()
    {
        return $this->sheetImport->importedCount;
    }
}
