<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientObjective;
use App\Models\ExpertiseManager;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

            $tableData = $tableData->with(['client_objective.client', 'client_objective.objective_manager', 'expertise_manager' ,'status_manager'])->get();
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'client_objective_id' => [
                'required',
                'integer',
                'exists:client_objectives,id',
            ],

            'expertise_manager_id' => [
                'required',
                'integer',
                'exists:expertise_managers,id',
            ],

            'task_due_date' => [
                'required',
                'date',
                // 'after_or_equal:now',
            ],
        ], [
            // Optional custom messages
            'client_objective_id.required' => 'Please select a client objective.',
            'expertise_manager_id.required' => 'Please select an expertise.',
            'task_due_date.required' => 'Please select due date & time.',
            // 'task_due_date.after_or_equal' => 'Task date must be today or future.',
        ]);

        try {
            $task = new Task();
            $task->fill($data);
            $task->created_by = auth()->id();
            $task->save();

            $this->syncActivities(
                $task,
                json_decode($request->commitments ?? '[]', true),
                json_decode($request->deliverables ?? '[]', true)
            );
            return response()->json(['success' => true, 'message' => 'Task created successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
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
        $userExpertises = $user->expertiseManagers()->activeExpertise()->get();

        $expertises = $userExpertises->isNotEmpty()
            ? $userExpertises
            : ExpertiseManager::activeExpertise()->get();

        $clientObjectives = ClientObjective::with(['client', 'objective_manager'])->get();

        /* ============================
            Defaults (NEW + EDIT)
        ============================ */
        $taskData = null;

        $today = Carbon::today()->toDateString();

        // ✅ TODAY MUST ALWAYS EXIST
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
                Collect dates
            ---------------------------- */
            $dates = $dates->merge(
                $taskData->commitments
                    ->pluck('due_date')
                    ->filter()
                    ->map(fn($d) => $d->toDateString())
            );

            $dates = $dates->merge(
                $taskData->deliverables
                    ->pluck('expected_date')
                    ->filter()
                    ->map(fn($d) => $d->toDateString())
            );

            /* Unique + sorted (today on top) */
            $dates = $dates->unique()
                ->sort()
                ->values()
                ->prepend($today)
                ->unique()
                ->values();

            /* ----------------------------
                Group by date
             ---------------------------- */
            $commitmentsByDate = $taskData->commitments->groupBy(
                fn($c) => $c->due_date->toDateString()
            );

            $deliverablesByDate = $taskData->deliverables->groupBy(
                fn($d) => $d->expected_date->toDateString()
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

        /* ============================
            Render modal
        ============================ */
        return view('admin.task.task-form', compact(
            'taskData',
            'clientObjectives',
            'expertises',
            'dates',
            'commitmentsByDate',
            'deliverablesByDate',
            'contentByDate'
        ));
    }

    public function syncActivities(Task $task, array $commitments = [], array $deliverables = []): void
    {
        /* ===========================
           Reset old data (update case)
        ============================ */
        $task->commitments()->delete();
        $task->deliverables()->delete();

        /* ===========================
           Store Commitments
        ============================ */
        foreach ($commitments as $date => $items) {
            foreach ($items as $item) {
                if (!empty($item['text'])) {
                    $task->commitments()->create([
                        'commitment' => $item['text'],
                        'due_date'   => $date,
                    ]);
                }
            }
        }

        /* ===========================
           Store Deliverables
        ============================ */
        foreach ($deliverables as $date => $items) {
            foreach ($items as $item) {
                if (!empty($item['text'])) {
                    $task->deliverables()->create([
                        'deliverable'    => $item['text'],
                        'expected_date'  => $date,
                    ]);
                }
            }
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
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $request->validate([
            'client_objective_id' => [
                'required',
                'integer',
                'exists:client_objectives,id',
            ],

            'expertise_manager_id' => [
                'required',
                'integer',
                'exists:expertise_managers,id',
            ],

            'task_due_date' => [
                'required',
                'date',
                // 'after_or_equal:today',
            ],
        ], [
            'client_objective_id.required' => 'Please select a client objective.',
            'expertise_manager_id.required' => 'Please select an expertise.',
            'task_due_date.required' => 'Please select due date & time.',
            // 'task_due_date.after_or_equal' => 'Task date cannot be in the past.',
        ]);


        try {
            $task = Task::findOrFail($id);

            $task->fill($data);
            $task->updated_by = auth()->id();
            $task->save();

            $this->syncActivities(
                $task,
                json_decode($request->commitments ?? '[]', true),
                json_decode($request->deliverables ?? '[]', true)
            );
            return response()->json(['success' => true, 'message' => 'Task updated successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
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
