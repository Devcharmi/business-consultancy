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
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ConsultingSheetImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $errors = [];
    public $importedCount = 0;
    protected $headersValidated = false;

    public function collection(Collection $rows)
    {
        logger('Import started');

        // 🔥 RESET every time collection runs
        // dd($rows->first()->keys());
        // $this->errors = [];
        // $this->importedCount = 0;

        if ($rows->isEmpty()) {
            $this->errors[] = [
                'row' => '-',
                'message' => 'The uploaded file contains no data.'
            ];
            return;
        }

        $hasRowData = false;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $clientName     = $row['client_name'] ?? null;
            $objectiveName  = $row['objective_name'] ?? null;
            $focusAreaName  = $row['focus_area_name'] ?? null;
            $expertiseName  = $row['expertise_name'] ?? null;

            // Parse date/time
            $consultingDateRaw = $row['consulting_date_yyyy_mm_dd'] ?? null;
            $startTimeRaw      = $row['start_time_hhmm'] ?? null;
            $endTimeRaw        = $row['end_time_hhmm'] ?? null;

            $consultingDate = $this->parseDate($consultingDateRaw, $rowNumber, 'consulting_date');
            $startTime      = $this->parseTime($startTimeRaw, $rowNumber, 'start_time');
            $endTime        = $this->parseTime($endTimeRaw, $rowNumber, 'end_time');

            logger([
                'client' => $clientName,
                'objective' => $objectiveName,
                'focus' => $focusAreaName,
                'expertise' => $expertiseName,
            ]);

            if (collect($row)->filter(function ($value) {
                return !is_null($value) && trim($value) !== '';
            })->isEmpty()) {
                continue;
            }

            $hasRowData = true;

            // Required field check
            if (!$clientName || !$objectiveName || !$focusAreaName || !$expertiseName) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Required fields missing.'
                ];
                continue;
            }

            // Client
            $client = Client::where('client_name', trim($clientName))->first();
            if (!$client) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => "Client '{$clientName}' not found."
                ];
                continue;
            }

            // Objective, Focus Area, Expertise
            $objective = ObjectiveManager::firstOrCreate(['name' => trim($objectiveName)], ['status' => 1]);
            $focusArea = FocusAreaManager::firstOrCreate(['name' => trim($focusAreaName)], ['status' => 1]);
            $expertise = ExpertiseManager::firstOrCreate(['name' => trim($expertiseName)], ['status' => 1]);

            // Client Objective
            $clientObjective = ClientObjective::firstOrCreate(
                ['client_id' => $client->id, 'objective_manager_id' => $objective->id],
                ['status' => 1]
            );


            // Validator like controller
            $validatorData = [
                'client_id' => $client->id,
                'objective_manager_id' => $objective->id,
                'expertise_manager_id' => $expertise->id,
                'focus_area_manager_id' => $focusArea->id,
                // 'consulting_date' => $consultingDate,
                // 'start_time' => $startTime,
                // 'end_time' => $endTime,
            ];

            $validator = Validator::make($validatorData, [
                'client_id' => 'required|integer|exists:clients,id',
                'objective_manager_id' => 'required|integer|exists:objective_managers,id',
                'expertise_manager_id' => 'required|integer|exists:expertise_managers,id',
                'focus_area_manager_id' => 'required|integer|exists:focus_area_managers,id',
                // 'consulting_date' => 'required|date',
                // 'start_time' => 'required',
                // 'end_time' => 'required|after:start_time',
            ], [
                'client_id.required' => 'Please select a client.',
                'objective_manager_id.required' => 'Please select a objective.',
                'expertise_manager_id.required' => 'Please select an expertise.',
                'focus_area_manager_id.required' => 'Please select a focus area.',
                // 'consulting_date.required' => 'Please select consulting date & time.',
                // 'start_time.required' => 'Please select start time.',
                // 'end_time.required' => 'Please select end time.',
                // 'end_time.after' => 'End time must be greater than start time.',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $this->errors[] = ['row' => $rowNumber, 'message' => $message];
                }
                continue;
            }

            // // Time overlap check
            // $overlap = Consulting::hasTimeOverlap(
            //     $consultingDate,
            //     $startTime,
            //     $endTime,
            //     $expertise->id
            // );

            // if ($overlap) {
            //     $this->errors[] = [
            //         'row' => $rowNumber,
            //         'message' => 'This time slot overlaps with an existing consulting.'
            //     ];
            //     continue;
            // }

            // Create record
            Consulting::create([
                'client_objective_id'   => $clientObjective->id,
                'focus_area_manager_id' => $focusArea->id,
                'expertise_manager_id'  => $expertise->id,
                'consulting_date'       => $consultingDate,
                'start_time'            => $startTime,
                'end_time'              => $endTime,
                'created_by'            => auth()->id() ?? null,
            ]);

            $this->importedCount++;
        }

        // Final checks
        if (!$hasRowData) {
            $this->errors[] = ['row' => '-', 'message' => 'File contains only empty rows.'];
        }

        if ($this->importedCount == 0 && $hasRowData && empty($this->errors)) {
            $this->errors[] = [
                'row' => '-',
                'message' => 'No valid rows found to import.'
            ];
        }
    }

    protected function parseDate($value, $rowNumber, $fieldName)
    {
        // if (!$value) {
        //     $this->errors[] = ['row' => $rowNumber, 'message' => "{$fieldName} is empty."];
        //     return null;
        // }

        try {
            if ($value) {
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
            }
        } catch (\Exception $e) {
            $this->errors[] = ['row' => $rowNumber, 'message' => "Invalid format for {$fieldName}: {$value}"];
            return null;
        }
    }

    protected function parseTime($value, $rowNumber, $fieldName)
    {
        // if (!$value) {
        //     $this->errors[] = ['row' => $rowNumber, 'message' => "{$fieldName} is empty."];
        //     return null;
        // }

        // Handle numeric Excel time
        if ($value) {
            if (is_numeric($value) && $value < 1) {
                try {
                    $dt = ExcelDate::excelToDateTimeObject($value);
                    return $dt->format('H:i');
                } catch (\Exception $e) {
                    $this->errors[] = ['row' => $rowNumber, 'message' => "Invalid numeric time for {$fieldName}: {$value}"];
                    return null;
                }
            }

            $value = str_pad($value, 4, '0', STR_PAD_LEFT);

            if (strlen($value) !== 4) {
                $this->errors[] = ['row' => $rowNumber, 'message' => "Invalid time format for {$fieldName}: {$value}"];
                return null;
            }

            return substr($value, 0, 2) . ':' . substr($value, 2, 2);
        }
    }
}
