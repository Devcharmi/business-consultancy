<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TaskPdf;
use App\Http\Controllers\Controller;
use App\Models\ClientObjective;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use App\Models\StatusManager;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskCommitment;
use App\Models\TaskContent;
use App\Models\TaskDeliverable;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:task.allow')->only(['index', 'show']);
        $this->middleware('permission:task.create')->only(['store']);
        $this->middleware('permission:task.edit')->only(['update']);
        $this->middleware('permission:task.delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            // Apply external filters
            $data['date_range']     = $request->get('dateRange');
            $data['filterClient']    = $request->get('filterClient');
            $data['filterObjective']  = $request->get('filterObjective');
            $data['filterExpertise'] = $request->get('filterExpertise');
            $data['filterCreatedBy'] = $request->get('filterCreatedBy');
            $data['filterStatus'] = $request->get('filterStatus');

            $columns = [
                'id',
                'title',
                'client_objective_id',
                'expertise_manager_id',
                'task_start_date',
                'task_due_date',
                'status_manager_id'
            ];

            $tableData = Task::query()
                ->accessibleBy(auth()->user())   // 👈 ADD HERE
                ->filters($data, $columns)
                ->select($columns);

            unset($data['start']);
            unset($data['length']);

            $tableDataCount = Task::query()
                ->accessibleBy(auth()->user())   // 👈 ADD HERE ALSO
                ->filters($data, $columns)
                ->count();

            $tableData = $tableData->with(['client_objective.client', 'client_objective.objective_manager', 'expertise_manager', 'status_manager'])->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();
            return $response;
        }
        $filterRouteConfig = config('filter.route_filters');

        $filters = filterDropdowns(array_keys($filterRouteConfig));
        return view('admin.task.index', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     */


    public function show(Request $request, $id)
    {
        $user = auth()->user();
        $consultingId = $request->query('consulting_id');

        $staffs = User::get();
        /* ============================
       Expertise & Status
        ============================ */
        $statuses = StatusManager::activeStatus()->get();

        $userExpertises = $user->expertiseManagers()->activeExpertise()->get();
        $expertises = $userExpertises->isNotEmpty()
            ? $userExpertises
            : ExpertiseManager::activeExpertise()->get();

        $clientObjectives = ClientObjective::with(['client', 'objective_manager'])->get();

        /* ============================
       Defaults (NEW TASK)
        ============================ */
        $taskData = null;
        $consultingData = null;

        $commitmentsByDate = collect();
        $deliverablesByDate = collect();
        $contentByDate = collect();

        /*
    |----------------------------------------
    | CREATE (NEW)
    |----------------------------------------
    */
        if ($id === 'new' && $consultingId) {

            $consultingData = Consulting::with([
                'client_objective.client',
                'client_objective.objective_manager',
                'expertise_manager'
            ])->findOrFail($consultingId);
        }

        /* ============================
       EDIT TASK
        ============================ */
        if ($id !== 'new') {

            $taskData = Task::with([
                'client_objective.client',
                'client_objective.objective_manager',
                'expertise_manager',
                'status_manager',
                'content',
                'commitments',
                'deliverables',
                'consulting'
            ])->findOrFail($id);

            // ✅ SET consultingData FROM taskData
            if ($taskData->consulting_id) {
                $consultingData = $taskData->consulting;
            }
            /* ----------------------------
                Collect ALL dates (normalized)
                ---------------------------- */
            // $dates = collect()
            //     ->merge($taskData->content->pluck('content_date')->map(fn($d) => Carbon::parse($d)->toDateString()))
            //     ->merge($taskData->commitments->pluck('commitment_date')->map(fn($d) => Carbon::parse($d)->toDateString()))
            //     ->merge($taskData->deliverables->pluck('deliverable_date')->map(fn($d) => Carbon::parse($d)->toDateString()))
            //     ->push($today)
            //     ->unique()
            //     ->sortDesc()
            //     ->values();

            /* ----------------------------
                Group data by DATE STRING
                ---------------------------- */
            $commitmentsByDate = $taskData->commitments
                ->groupBy(fn($c) => Carbon::parse($c->commitment_date)->toDateString());

            $deliverablesByDate = $taskData->deliverables
                ->groupBy(fn($d) => Carbon::parse($d->deliverable_date)->toDateString());

            $contentByDate = $taskData->content
                ->keyBy(fn($c) => Carbon::parse($c->content_date)->toDateString());
        }

        $clientObjectiveId = null;
        $expertiseManagerId = null;

        if ($consultingData) {
            $clientObjectiveId = $consultingData->client_objective_id ?? null;
            $expertiseManagerId = $consultingData->expertise_manager_id ?? null;
        }

        $today = Carbon::today()->toDateString();

        if ($taskData) {
            // EDIT MODE
            $date = Carbon::parse($taskData->task_start_date)->toDateString();
        } elseif ($consultingData && $consultingData->consulting_datetime) {
            // CREATE FROM CONSULTING
            $date = Carbon::parse($consultingData->consulting_datetime)->toDateString();
        } else {
            // NORMAL NEW
            $date = $today;
        }

        $dates = collect([$date]);
        // dd($commitmentsByDate);

        /* ============================
        Render View
        ============================ */
        return view('admin.task.task-form', compact(
            'taskData',
            'consultingData',
            'clientObjectives',
            'expertises',
            'statuses',
            'dates',
            'commitmentsByDate',
            'deliverablesByDate',
            'contentByDate',
            'clientObjectiveId',
            'expertiseManagerId',
            'staffs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'client_objective_id' => ['required', 'integer', 'exists:client_objectives,id'],
            'expertise_manager_id' => ['required', 'integer', 'exists:expertise_managers,id'],
            'task_start_date' => ['required', 'date'],
            'task_due_date' => ['required', 'date', 'after_or_equal:task_start_date'],
        ]);

        try {
            DB::beginTransaction();

            $task = new Task();
            $task->fill($request->all());
            $task->created_by = auth()->id();
            $task->save();

            // ✅ CONTENT
            $this->syncTaskContent($task, $request->content);

            // ✅ Commitments
            $this->syncCommitmentActivities(
                $task,
                json_decode($request->commitments, true) ?? [],
                json_decode($request->commitments_to_delete, true) ?? [],
                $request->commitments_existing ?? []
            );

            // ✅ Deliverables
            $this->syncDeliverableActivities(
                $task,
                json_decode($request->deliverables, true) ?? [],
                json_decode($request->deliverables_to_delete, true) ?? [],
                $request->deliverables_existing ?? []
            );

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $path = $file->store('task_attachments', 'public');

                    TaskAttachment::create([
                        'task_id'        => $task->id,
                        'file_name'      => basename($path),
                        'file_path'      => $path,
                        'file_type'      => $file->getClientMimeType(),
                        'file_size'      => $file->getSize(),
                        'original_name'  => $file->getClientOriginalName(),
                        'storage'        => 'public',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required'],
            'client_objective_id' => ['required', 'integer', 'exists:client_objectives,id'],
            'expertise_manager_id' => ['required', 'integer', 'exists:expertise_managers,id'],
            'task_start_date' => ['required', 'date'],
            'task_due_date' => ['required', 'date', 'after_or_equal:task_start_date'],
        ]);

        try {
            DB::beginTransaction();

            $task->update($request->only([
                'client_objective_id',
                'title',
                'expertise_manager_id',
                'task_start_date',
                'task_due_date',
                'type',
                'status_manager_id',
            ]));

            // ✅ Content
            $this->syncTaskContent($task, $request->content);

            // dd($request->commitments_existing);
            // ✅ Commitments
            $this->syncCommitmentActivities(
                $task,
                json_decode($request->commitments, true) ?? [],
                json_decode($request->commitments_to_delete, true) ?? [],
                $request->commitments_existing ?? []
            );

            // ✅ Deliverables
            $this->syncDeliverableActivities(
                $task,
                json_decode($request->deliverables, true) ?? [],
                json_decode($request->deliverables_to_delete, true) ?? [],
                $request->deliverables_existing ?? []
            );

            if ($request->filled('existing_file_names')) {
                foreach ($request->existing_file_names as $id => $name) {

                    $attachment = TaskAttachment::where('id', $id)
                        ->where('task_id', $task->id)
                        ->first();

                    if (!$attachment) {
                        continue; // attachment was deleted
                    }

                    $extension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);

                    $attachment->update([
                        'original_name' => $name
                            ? trim($name) . '.' . $extension
                            : $attachment->original_name,
                    ]);
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $path = $file->store('task_attachments', 'public');

                    TaskAttachment::create([
                        'task_id'        => $task->id,
                        'file_name'      => basename($path),
                        'file_path'      => $path,
                        'file_type'      => $file->getClientMimeType(),
                        'file_size'      => $file->getSize(),
                        'original_name'  => $file->getClientOriginalName(),
                        'storage'        => 'public',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    private function syncTaskContent(Task $task, ?array $contents): void
    {
        if (empty($contents)) return;

        foreach ($contents as $date => $html) {

            if (blank(strip_tags($html))) {
                continue;
            }

            $contentDate = Carbon::parse($date)->toDateString();

            TaskContent::updateOrCreate(
                [
                    'task_id'      => $task->id,
                    'content_date' => $contentDate,
                ],
                [
                    'task_content' => $html,
                ]
            );
        }
    }

    public function syncCommitmentActivities(
        Task $task,
        array $commitments = [],
        array $commitmentsToDelete = [],
        array $commitmentsExisting = []
    ) {
        // ---------------- DELETE ----------------
        if (!empty($commitmentsToDelete)) {
            TaskCommitment::whereIn('id', $commitmentsToDelete)->delete();
        }

        // ---------------- UPDATE EXISTING ----------------
        foreach ($commitmentsExisting as $id => $item) {

            if (blank($item['text'])) {
                continue;
            }

            $commitment = TaskCommitment::where('id', $id)
                ->where('task_id', $task->id)
                ->first();

            if ($commitment) {
                // TaskCommitment::where('id', $id)
                //     ->where('task_id', $task->id)
                //     ->update([
                //         'commitment' => $item['text'],
                //         'due_date' => $item['due_date']
                //     ]);
                $commitment->update([
                    'commitment' => $item['text'],
                    'due_date'   => $item['due_date'],
                    'status'        => $item['status'] ?? 1,
                    'staff_manager_id' => $item['staff_manager_id'] ?? 1,
                ]);
            }
        }

        // ---------------- CREATE NEW ----------------
        foreach ($commitments as $date => $items) {

            if (empty($items) || !is_array($items)) {
                continue;
            }

            $date = Carbon::parse($date)->toDateString();

            foreach ($items as $item) {

                if (blank($item['text']) || empty($item['commitment_due_date'])) {
                    continue;
                }

                // ⛔ skip existing DB rows
                if (!empty($item['id'])) {
                    continue;
                }

                TaskCommitment::create([
                    'task_id'         => $task->id,
                    'commitment_date' => $date,
                    'commitment'      => $item['text'],
                    'due_date'        => $item['commitment_due_date'],
                    'status'          => $item['status'] ?? 1,
                    'staff_manager_id' => $item['staff_manager_id'] ?? 1,
                ]);
            }
        }
    }

    public function syncDeliverableActivities(
        Task $task,
        array $deliverables = [],
        array $deliverablesToDelete = [],
        array $deliverablesExisting = []
    ) {
        // ---------------- DELETE ----------------
        if (!empty($deliverablesToDelete)) {
            TaskDeliverable::whereIn('id', $deliverablesToDelete)->delete();
        }

        // ---------------- UPDATE EXISTING ----------------
        foreach ($deliverablesExisting as $id => $item) {

            if (blank($item['text'])) {
                continue;
            }

            $commitment = TaskDeliverable::where('id', $id)
                ->where('task_id', $task->id)
                ->first();

            if ($commitment) {
                $commitment->update([
                    'deliverable' => $item['text'],
                    'expected_date'   => $item['expected_date'],
                    'status'        => $item['status'] ?? 1,
                ]);
            }
        }

        // ---------------- CREATE NEW ----------------
        foreach ($deliverables as $date => $items) {

            if (empty($items) || !is_array($items)) {
                continue;
            }

            $date = Carbon::parse($date)->toDateString();

            foreach ($items as $item) {

                if (blank($item['text'])) {
                    continue;
                }

                // ⛔ skip existing DB rows
                if (!empty($item['id'])) {
                    continue;
                }

                TaskDeliverable::create([
                    'task_id'          => $task->id,
                    'deliverable_date' => $date,
                    'deliverable'      => $item['text'],
                    'expected_date'    => $item['expected_date'] ?? $date,
                    'status'           => $item['status'] ?? 1,
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            return response()->json(['success' => true, 'message' => 'Task deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function destroyAttachment(TaskAttachment $attachment)
    {
        // delete file
        if (Storage::disk($attachment->storage)->exists($attachment->file_path)) {
            Storage::disk($attachment->storage)->delete($attachment->file_path);
        }

        // delete DB record
        $attachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attachment deleted'
        ]);
    }

    public function exportTaskPdf($id)
    {
        $task = Task::with([
            'client_objective.client',
            'content',
            'commitments.userTask.status_manager',
            'deliverables.userTask.status_manager'
        ])->findOrFail($id);

        $pdf = new TaskPdf('P', 'mm', 'A4');
        $pdf->setTask($task);

        $pdf->SetCreator('Laravel');
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Task Report - ' . $task->title);

        // 🔥 Margins INSIDE letterhead
        $pdf->SetMargins(15, 55, 5);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
        $pdf->SetAutoPageBreak(true, 25);

        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 10);

        // Blade = CONTENT ONLY
        $html = view('admin.pdf.task-content', compact('task'))->render();

        $pdf->writeHTML($html, true, false, false, false, '');

        return $pdf->Output('task-report.pdf', 'I');
    }

    public function taskPdf($id)
    {
        $task = Task::with([
            'client_objective.client',
            'client_objective.objective_manager',
            'expertise_manager',
            'status_manager',
            'createdBy',

            'commitments.staff',
            'commitments.userTask.status_manager',

            'deliverables.userTask.status_manager',

            'content',
        ])->findOrFail($id);

        /**
         * 🔥 BUILD TIMELINE (ONCE)
         */
        $timeline = [];

        foreach ($task->content as $c) {
            if ($c->content_date) {
                $date = $c->content_date->toDateString();
                $timeline[$date]['content'][] = $c;
            }
        }

        foreach ($task->commitments as $c) {
            if ($c->commitment_date) {
                $date = $c->commitment_date->toDateString();
                $timeline[$date]['commitments'][] = $c;
            }
        }

        foreach ($task->deliverables as $d) {
            if ($d->deliverable_date) {
                $date = $d->deliverable_date->toDateString();
                $timeline[$date]['deliverables'][] = $d;
            }
        }

        // Sort dates DESC
        krsort($timeline);

        // 🔥 Prepare safe file name
        $clientName = $task->client_objective->client->name ?? 'Client';
        $expertiseName = $task->expertise_manager->name ?? 'Expertise';

        $fileName = "Task-{$task->id}-" .
            preg_replace('/[^A-Za-z0-9\-]/', '_', $clientName) . '-' .
            preg_replace('/[^A-Za-z0-9\-]/', '_', $expertiseName) . '.pdf';

        $pdf = Pdf::loadView(
            'admin.pdf.task-content',
            compact('task', 'timeline')
        )
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'dejavusans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
                'dpi' => 150,
            ]);

        return $pdf->stream($fileName);
    }

    // public function taskPdf($id)
    // {
    //     $task = Task::with([
    //         'client_objective.client',
    //         'client_objective.objective_manager',
    //         'expertise_manager',
    //         'status_manager',
    //         'createdBy',

    //         // Commitments + status
    //         'commitments.userTask.status_manager',
    //         'commitments.staff',

    //         // Deliverables + status
    //         'deliverables.userTask.status_manager',

    //         'content',
    //     ])->findOrFail($id);

    //     $pdf = Pdf::loadView('admin.pdf.task-content', compact('task'))
    //         ->setPaper('A4', 'portrait');

    //     return $pdf->stream("task-{$task->id}.pdf");
    // }

    // public function taskPdf($id)
    // {
    //     $task = Task::with([
    //         'client_objective.client',
    //         'client_objective.objective_manager',
    //         'expertise_manager',
    //         'status_manager',
    //         'commitments' => function ($q) {
    //             $q->orderBy('commitment_date', 'desc'); // date-wise desc
    //         },
    //         'deliverables' => function ($q) {
    //             $q->orderBy('deliverable_date', 'desc');
    //         },
    //         'content' => function ($q) {
    //             $q->orderBy('content_date', 'desc');
    //         },
    //     ])->findOrFail($id);

    //     // Group commitments date-wise
    //     $commitmentsByDate = $task->commitments->groupBy('date');

    //     // ---------------- FILE NAME ----------------
    //     $clientName = $task->client_objective->client->client_name ?? 'client';
    //     $safeClient = Str::slug($clientName);

    //     $date = $task->task_due_date
    //         ? \Carbon\Carbon::parse($task->task_due_date)->format('Y-m-d')
    //         : now()->format('Y-m-d');

    //     $fileName = "task-{$safeClient}-{$date}-{$task->id}.pdf";
    //     // ------------------------------------------------

    //     $pdf = Pdf::loadView('admin.pdf.task-content', compact('task', 'commitmentsByDate'))
    //         ->setPaper('A4', 'portrait');

    //     return $pdf->stream($fileName);
    //     // return $pdf->download($fileName);
    // }
}
