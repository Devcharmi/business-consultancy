<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientObjective;
use App\Models\ExpertiseManager;
use App\Models\StatusManager;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskCommitment;
use App\Models\TaskContent;
use App\Models\TaskDeliverable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            $columns = [
                'id',
                'client_objective_id',
                'expertise_manager_id',
                'task_due_date',
                'status_manager_id'
            ];

            $tableData = Task::Filters($data, $columns)
                ->select($columns);

            unset($data['start']);
            unset($data['length']);

            $tableDataCount = Task::Filters($data, $columns)->count();

            $tableData = $tableData->with(['client_objective.client', 'client_objective.objective_manager', 'expertise_manager', 'status_manager'])->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();
            return $response;
        }
        return view('admin.task.index');
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
        $clientObjectiveId   = $request->query('client_objective_id');
        $expertiseManagerId  = $request->query('expertise_manager_id');
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
        $today = Carbon::today()->toDateString();

        $dates = collect([$today]);
        $commitmentsByDate = collect();
        $deliverablesByDate = collect();
        $contentByDate = collect();

        /* ============================
       EDIT TASK
        ============================ */
        if ($id !== 'new') {

            $taskData = Task::with([
                'client_objective',
                'expertise_manager',
                'status_manager',
                'content',
                'commitments',
                'deliverables',
            ])->findOrFail($id);

            /* ----------------------------
            Collect ALL dates
            ---------------------------- */
            $dates = collect()
                ->merge($taskData->content->pluck('content_date'))
                ->merge($taskData->commitments->pluck('commitment_date'))
                ->merge($taskData->deliverables->pluck('deliverable_date'))
                ->push($today) // ensure today exists
                ->filter()
                ->unique()
                ->sortDesc()
                ->values();

            /* ----------------------------
            Group data by DATE
            ---------------------------- */
            $commitmentsByDate = $taskData->commitments->groupBy('commitment_date');
            $deliverablesByDate = $taskData->deliverables->groupBy('deliverable_date');
            $contentByDate = $taskData->content->keyBy('content_date');
        }

        /* ============================
        Render View
        ============================ */
        return view('admin.task.task-form', compact(
            'taskData',
            'clientObjectives',
            'expertises',
            'statuses',
            'dates',
            'commitmentsByDate',
            'deliverablesByDate',
            'contentByDate',
            'clientObjectiveId',
            'expertiseManagerId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_objective_id' => ['required', 'integer', 'exists:client_objectives,id'],
            'expertise_manager_id' => ['required', 'integer', 'exists:expertise_managers,id'],
            'task_due_date' => ['required', 'date'],
        ]);

        try {
            DB::beginTransaction();

            $task = new Task();
            $task->fill($request->all());
            $task->created_by = auth()->id();
            $task->save();

            // ✅ CONTENT
            $this->syncTaskContent($task, $request->content);

            $this->syncActivities(
                $task,
                json_decode($request->commitments ?? '[]', true),
                json_decode($request->deliverables ?? '[]', true),
                json_decode($request->commitments_to_delete ?? '[]', true),
                json_decode($request->deliverables_to_delete ?? '[]', true)
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
            'client_objective_id' => ['required', 'integer', 'exists:client_objectives,id'],
            'expertise_manager_id' => ['required', 'integer', 'exists:expertise_managers,id'],
            'task_due_date' => ['required', 'date'],
        ]);

        try {
            DB::beginTransaction();

            $task->update($request->only([
                'client_objective_id',
                'title',
                'expertise_manager_id',
                'task_due_date',
                'type',
                'status_manager_id',
            ]));

            // ✅ Content
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

            TaskCommitment::where('id', $id)
                ->where('task_id', $task->id)
                ->update([
                    'commitment' => $item['text'],
                    'due_date'   => $item['due_date'],
                ]);
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

            TaskDeliverable::where('id', $id)
                ->where('task_id', $task->id)
                ->update([
                    'deliverable'   => $item['text'],
                    'expected_date' => $item['expected_date'] ?? null,
                    'status'        => $item['status'] ?? 1,
                ]);
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
}
