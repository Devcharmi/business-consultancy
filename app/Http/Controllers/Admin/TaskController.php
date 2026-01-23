<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientObjective;
use App\Models\ExpertiseManager;
use App\Models\StatusManager;
use App\Models\Task;
use App\Models\TaskCommitment;
use App\Models\TaskDeliverable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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


    public function show($id)
    {
        $user = auth()->user();

        /* ============================
        Expertise logic
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

        // ✅ Accordion dates (always include today)
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
            Collect accordion dates
            (ONLY created_at)
             ---------------------------- */
            $dates = $dates->merge(
                $taskData->commitments
                    ->pluck('created_at')
                    ->filter()
                    ->map(fn($d) => $d->toDateString())
            );

            $dates = $dates->merge(
                $taskData->deliverables
                    ->pluck('created_at')
                    ->filter()
                    ->map(fn($d) => $d->toDateString())
            );

            if ($taskData->content) {
                $dates->push($taskData->content->created_at->toDateString());
            }

            /* ----------------------------
            Unique + sort (today on top)
            ---------------------------- */
            $dates = $dates
                ->unique()
                ->sort()
                ->values();

            if (!$dates->contains($today)) {
                $dates->prepend($today);
            }

            /* ----------------------------
            Group records by created_at
            ---------------------------- */
            // Group by CREATED date (accordion key)
            $commitmentsByDate = $taskData->commitments->groupBy(
                fn($c) => $c->created_at->toDateString()
            );

            $deliverablesByDate = $taskData->deliverables->groupBy(
                fn($d) => $d->created_at->toDateString()
            );


            /* ----------------------------
            Content (1 per day)
            ---------------------------- */
            if ($taskData->content) {
                $contentByDate->put(
                    $taskData->content->created_at->toDateString(),
                    $taskData->content
                );
            }
        }
        // dd($taskData);
        /* ============================
        Render view
        ============================ */
        return view('admin.task.task-form', compact(
            'taskData',
            'clientObjectives',
            'expertises',
            'statuses',
            'dates',
            'commitmentsByDate',
            'deliverablesByDate',
            'contentByDate'
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
                json_decode($request->deliverables ?? '[]', true)
            );
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

    private function syncTaskContent(Task $task, ?array $contents): void
    {
        if (empty($contents)) {
            return;
        }

        foreach ($contents as $date => $html) {

            // Remove empty CKEditor garbage
            if (blank(strip_tags($html))) {
                continue;
            }

            $date = Carbon::parse($date)->startOfDay();

            $task->content()->updateOrCreate(
                [
                    'task_id'    => $task->id,
                    'created_at' => $date,
                ],
                [
                    'task_content' => $html,
                    'updated_at'   => now(),
                ]
            );
        }
    }

    public function syncActivities(
        Task $task,
        array $commitments = [],
        array $deliverables = []
    ): void {

        /* ===========================
       Reset old data (update case)
        ============================ */
        $task->commitments()->delete();
        $task->deliverables()->delete();

        /* ===========================
       Prepare bulk inserts
        ============================ */
        $commitmentInsert = [];
        $deliverableInsert = [];

        /* ===========================
       Commitments
        ============================ */
        foreach ($commitments as $accordionDate => $items) {
            foreach ($items as $item) {

                if (empty($item['text'])) {
                    continue;
                }

                $commitmentInsert[] = [
                    'task_id'     => $task->id,
                    'commitment'  => $item['text'],
                    'due_date'    => $item['commitment_due_date'] ?? $accordionDate,
                    'status'      => $item['status'] ?? 1,
                    'created_at'  => isset($item['created_at'])
                        ? Carbon::parse($item['created_at'])
                        : now(),
                    'updated_at'  => now(),
                ];
            }
        }

        /* ===========================
       Deliverables
        ============================ */
        foreach ($deliverables as $accordionDate => $items) {
            foreach ($items as $item) {

                if (empty($item['text'])) {
                    continue;
                }

                $deliverableInsert[] = [
                    'task_id'       => $task->id,
                    'deliverable'   => $item['text'],
                    'expected_date' => $item['expected_date'] ?? $accordionDate,
                    'status'        => $item['status'] ?? 1,
                    'created_at'    => isset($item['created_at'])
                        ? Carbon::parse($item['created_at'])
                        : now(),
                    'updated_at'    => now(),
                ];
            }
        }

        /* ===========================
       Bulk insert (FAST 🚀)
        ============================ */
        if (!empty($commitmentInsert)) {
            TaskCommitment::insert($commitmentInsert);
        }

        if (!empty($deliverableInsert)) {
            TaskDeliverable::insert($deliverableInsert);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'client_objective_id' => ['required', 'integer', 'exists:client_objectives,id'],
            'expertise_manager_id' => ['required', 'integer', 'exists:expertise_managers,id'],
            'task_due_date' => ['required', 'date'],
        ]);

        try {
            DB::beginTransaction();
            // dd($request->all());
            $task->update($request->only([
                'client_objective_id',
                'title',
                'expertise_manager_id',
                'task_due_date',
                'type',
                'status_manager_id',
            ]));

            // ✅ CONTENT
            $this->syncTaskContent($task, $request->content);

            $this->syncActivities(
                $task,
                json_decode($request->commitments ?? '[]', true),
                json_decode($request->deliverables ?? '[]', true)
            );

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
}
