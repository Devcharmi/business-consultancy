<?php

namespace App\Imports;

use App\Models\UserTask;
use App\Models\User;
use App\Models\Client;
use App\Models\Lead;
use App\Models\PriorityManager;
use App\Models\StatusManager;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class UserTaskSheetImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $errors = [];
    public $importedCount = 0;

    public function collection(Collection $rows)
    {

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

            $taskName   = $row['task_name'] ?? null;
            $staffName  = $row['staff_name'] ?? null;
            $clientName = $row['client_name'] ?? null;
            // $leadName   = $row['lead_name'] ?? null;

            $priorityName = $row['priority'] ?? null;
            $statusName   = $row['status'] ?? null;

            // $entityType = $row['entity_type'] ?? null;
            $taskType   = $row['task_type'] ?? null;
            // $sourceType = $row['source_type'] ?? null;

            $startDate = $this->parseDate($row['task_start_date'] ?? null, $rowNumber, 'task_start_date');
            $dueDate   = $this->parseDate($row['task_due_date'] ?? null, $rowNumber, 'task_due_date');
            $endDate   = $this->parseDate($row['task_end_date'] ?? null, $rowNumber, 'task_end_date');

            if (collect($row)->filter(function ($value) {
                return !is_null($value) && trim($value) !== '';
            })->isEmpty()) {
                continue;
            }

            $hasRowData = true;

            if (!$taskName || !$staffName) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => 'Task Name and Staff are required.'
                ];
                continue;
            }

            // Staff
            $staff = User::where('name', trim($staffName))->first();

            if (!$staff) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => "Staff '{$staffName}' not found."
                ];
                continue;
            }

            // Client
            $client = null;
            if ($clientName) {
                $client = Client::where('client_name', trim($clientName))->first();

                if (!$client) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => "Client '{$clientName}' not found."
                    ];
                    continue;
                }
            }

            // // Lead
            // $lead = null;
            // if ($leadName) {
            //     $lead = Lead::where('name', trim($leadName))->first();

            //     if (!$lead) {
            //         $this->errors[] = [
            //             'row' => $rowNumber,
            //             'message' => "Lead '{$leadName}' not found."
            //         ];
            //         continue;
            //     }
            // }

            // Priority
            $priority = null;
            if ($priorityName) {
                $priority = PriorityManager::firstOrCreate([
                    'name' => trim($priorityName)
                ]);
            }

            // Status
            $status = null;
            if ($statusName) {
                $status = StatusManager::firstOrCreate([
                    'name' => trim($statusName)
                ]);
            }

            $validatorData = [

                'task_name' => $taskName,
                'staff_manager_id' => $staff->id,
                // 'entity_type' => $entityType,
                'task_type' => $taskType,
            ];

            $validator = Validator::make($validatorData, [

                'task_name' => 'required|string|max:255',
                'staff_manager_id' => 'required|exists:users,id',
                // 'entity_type' => 'nullable|in:lead,client',
                'task_type' => 'nullable|in:task,meeting',

            ]);

            if ($validator->fails()) {

                foreach ($validator->errors()->all() as $message) {

                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => $message
                    ];
                }

                continue;
            }

            UserTask::create([

                'task_name' => $taskName,
                'staff_manager_id' => $staff->id,
                'client_id' => $client?->id,
                // 'lead_id' => $lead?->id,

                'priority_manager_id' => $priority?->id,
                'status_manager_id' => $status?->id,

                // 'entity_type' => $entityType,
                'task_type' => $taskType,
                // 'source_type' => $sourceType,

                'task_start_date' => $startDate,
                'task_due_date' => $dueDate,
                'task_end_date' => $endDate,

                'description' => $row['description'] ?? null,

                'created_by' => auth()->id(),
            ]);

            $this->importedCount++;
        }

        if (!$hasRowData) {

            $this->errors[] = [
                'row' => '-',
                'message' => 'File contains only empty rows.'
            ];
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

        try {

            if ($value) {

                if (is_numeric($value)) {

                    return Carbon::parse(
                        ExcelDate::excelToDateTimeObject($value)
                    )->format('Y-m-d');
                }

                $formats = ['Y-m-d', 'd-m-Y', 'm/d/Y'];

                foreach ($formats as $format) {

                    try {

                        return Carbon::createFromFormat($format, $value)
                            ->format('Y-m-d');
                    } catch (\Exception $e) {
                    }
                }

                throw new \Exception("No matching format");
            }
        } catch (\Exception $e) {

            $this->errors[] = [
                'row' => $rowNumber,
                'message' => "Invalid format for {$fieldName}: {$value}"
            ];

            return null;
        }
    }
}
