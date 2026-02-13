<?php

namespace App\Imports;

use App\Models\ObjectiveManager;
use Maatwebsite\Excel\Concerns\ToModel;

class ObjectiveManagerImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ObjectiveManager([
            //
        ]);
    }
}
