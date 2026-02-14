<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\ObjectiveManager;
use App\Models\FocusAreaManager;
use App\Models\ClientObjective;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ConsultingImportOld implements ToCollection, WithHeadingRow
{
    public $errors = [];
    public $importedCount = 0;

    public function collection(Collection $rows)
    {
        // dd($rows->first()->keys());
        // 0 => "client_name"
        // 1 => "objective_name"
        // 2 => "expertise_name"
        // 3 => "focus_area_name"
        // 4 => "consulting_date_yyyy_mm_dd"
        // 5 => "start_time_hhmm"
        // 6 => "end_time_hhmm"
        if ($rows->isEmpty()) {
            $this->errors[] = [
                'row' => '-',
                'message' => 'The uploaded file contains no data.'
            ];
            return;
        }

        $requiredHeaders = ['client_name', 'objective_name', 'expertise_name', 'focus_area_name'];

        $firstRow = $rows->first();

        if (!$firstRow) {
            $this->errors[] = [
                'row' => '-',
                'message' => 'File structure is invalid.'
            ];
            return;
        }

        $actualHeaders = array_keys($firstRow->toArray());

        foreach ($requiredHeaders as $header) {
            if (!in_array($header, $actualHeaders)) {
                $this->errors[] = [
                    'row' => '-',
                    'message' => "Missing required column: {$header}"
                ];
            }
        }

        if (!empty($this->errors)) {
            // dd($this->errors);
            return;
        }

        // 🔥 ADD THIS FLAG
        $hasRowData = false;

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2;

            $clientName     = $row['client_name'] ?? null;
            $objectiveName  = $row['objective_name'] ?? null;
            $focusAreaName  = $row['focus_area_name'] ?? null;
            $expertiseName  = $row['expertise_name'] ?? null;

            // Skip fully empty rows
            if (!$clientName && !$objectiveName && !$focusAreaName && !$expertiseName) {
                continue;
            }

            $hasRowData = true; // 🔥 Mark that we had at least one row

            // Required field validation
            if (!$clientName || !$objectiveName || !$focusAreaName || !$expertiseName) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Required fields missing.'
                ];
                continue;
            }

            // 1️⃣ Client Check
            $client = Client::where('client_name', trim($clientName))->first();

            if (!$client) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => "Client '{$clientName}' not found."
                ];
                continue;
            }

            // 2️⃣ Objective
            $objective = ObjectiveManager::firstOrCreate(
                ['name' => trim($objectiveName)],
                ['status' => 1]
            );

            // 3️⃣ Focus Area
            $focusArea = FocusAreaManager::firstOrCreate(
                ['name' => trim($focusAreaName)],
                ['status' => 1]
            );

            // 3️⃣ Expertise
            $expertise = ExpertiseManager::firstOrCreate(
                ['name' => trim($expertiseName)],
                ['status' => 1]
            );

            // 4️⃣ Client Objective
            $clientObjective = ClientObjective::firstOrCreate(
                [
                    'client_id' => $client->id,
                    'objective_manager_id' => $objective->id
                ],
                ['status' => 1]
            );


            // Example fields
            $consultingDateRaw = $row['consulting_date_yyyy_mm_dd'] ?? null;
            $startTimeRaw      = $row['start_time_hhmm'] ?? null;
            $endTimeRaw        = $row['end_time_hhmm'] ?? null;

            // Parse safely using separate methods
            $consultingDate = $this->parseDate($consultingDateRaw, $rowNumber, 'consulting_date');
            $startTime      = $this->parseTime($startTimeRaw, $rowNumber, 'start_time');
            $endTime        = $this->parseTime($endTimeRaw, $rowNumber, 'end_time');

            // Skip if date parsing failed
            if (!$consultingDate || !$startTime || !$endTime) {
                continue;
            }

            // Assuming you have authenticated user
            $userId = auth()->id() ?? null;
            // dd([
            //     $clientObjective->id,
            //     $focusArea->id,
            //     $expertise->id,
            //     $consultingDate,
            //     $startTime,
            //     $endTime,
            //     $userId,
            // ]);
            // ✅ Create Consulting
            Consulting::create([
                'client_objective_id'   => $clientObjective->id,
                'focus_area_manager_id' => $focusArea->id,
                'expertise_manager_id'  => $expertise->id,
                'consulting_date'       => $consultingDate,
                'start_time'            => $startTime,
                'end_time'              => $endTime,
                'created_by'            => $userId,
            ]);


            $this->importedCount++;
        }

        // 🔥 FINAL CHECK
        if (!$hasRowData) {
            $this->errors[] = [
                'row' => '-',
                'message' => 'File contains only empty rows.'
            ];
        }

        if ($this->importedCount == 0 && $hasRowData) {
            $this->errors[] = [
                'row' => '-',
                'message' => 'No valid rows found to import.'
            ];
        }
    }
    /**
     * Safely parse Excel / string date to Y-m-d
     */
    protected function parseDate($value, $rowNumber, $fieldName)
    {
        if (!$value) {
            $this->errors[] = ['row' => $rowNumber, 'message' => "{$fieldName} is empty."];
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::parse(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            $formats = ['Y-m-d', 'd-m-Y', 'm/d/Y'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                } catch (\Exception $e) {
                }
            }

            throw new \Exception("No matching date format");
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $rowNumber,
                'message' => "Invalid format for {$fieldName}: {$value}"
            ];
            return null;
        }
    }

    protected function parseTime($value, $rowNumber, $fieldName)
    {
        if (!$value) {
            $this->errors[] = [
                'row' => $rowNumber,
                'message' => "{$fieldName} is empty."
            ];
            return null;
        }

        // Handle numeric Excel time values (fractions of a day)
        if (is_numeric($value) && $value < 1) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($value);
                return $dt->format('H:i');
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => "Invalid numeric time for {$fieldName}: {$value}"
                ];
                return null;
            }
        }

        // If string, normalize
        $value = str_pad($value, 4, '0', STR_PAD_LEFT); // ensures 4 digits

        if (strlen($value) !== 4) {
            $this->errors[] = [
                'row' => $rowNumber,
                'message' => "Invalid time format for {$fieldName}: {$value}"
            ];
            return null;
        }

        return substr($value, 0, 2) . ':' . substr($value, 2, 2);
    }
}
