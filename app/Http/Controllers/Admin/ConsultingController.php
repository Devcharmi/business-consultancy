<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientObjective;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use App\Models\FocusAreaManager;
use App\Models\StatusManager;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsultingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:consulting.allow')->only(['index', 'show']);
        $this->middleware('permission:consulting.create')->only(['store']);
        $this->middleware('permission:consulting.edit')->only(['update']);
        $this->middleware('permission:consulting.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $data['date_range']     = $request->get('dateRange');
            $data['filterClient']    = $request->get('filterClient');
            $data['filterObjective']  = $request->get('filterObjective');
            $data['filterExpertise'] = $request->get('filterExpertise');
            $data['filterFocusArea'] = $request->get('filterFocusArea');

            $columns = [
                'id',
                'client_objective_id',
                'expertise_manager_id',
                'focus_area_manager_id',
                'consulting_datetime',
            ];

            $tableData = Consulting::Filters($data, $columns)
                ->select($columns);

            unset($data['start']);
            unset($data['length']);

            $tableDataCount = Consulting::Filters($data, $columns)->count();

            $tableData = $tableData->with(['client_objective.client', 'client_objective.objective_manager', 'expertise_manager', 'focus_area_manager'])->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        $filterRouteConfig = config('filter.route_filters');

        $filters = filterDropdowns(array_keys($filterRouteConfig));
        return view('admin.consulting.index', array_merge(
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
            'focus_area_manager_id' => [
                'required',
                'integer',
                'exists:focus_area_managers,id',
            ],
            'consulting_datetime' => [
                'required',
                'date',
                // 'after_or_equal:now',
            ],
        ], [
            // Optional custom messages
            'client_objective_id.required' => 'Please select a client objective.',
            'expertise_manager_id.required' => 'Please select an expertise.',
            'focus_area_manager_id.required' => 'Please select a focus area.',
            'consulting_datetime.required' => 'Please select consulting date & time.',
            // 'consulting_datetime.after_or_equal' => 'Consulting date must be today or future.',
        ]);

        try {
            // Start transaction

            // Create consulting
            $consulting = new Consulting();
            $consulting->fill($data);
            $consulting->created_by = auth()->id();
            $consulting->save();

            $focusArea = FocusAreaManager::find($data['focus_area_manager_id']);

            $task = $this->createTaskFromConsulting($consulting, $focusArea);

            $responseData = [
                'success' => true,
                'message' => 'Consulting created successfully',
                'consulting_id' => $consulting->id,
                'task_id' => $task->id
            ];


            return response()->json($responseData, 200);
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

        /* Get assigned expertises for logged-in user */
        $userExpertises = $user->expertiseManagers()->activeExpertise()->get();
        if ($userExpertises->isNotEmpty()) {
            // ✅ Show only allotted expertises
            $expertises = $userExpertises;
        } else {
            // ✅ If none allotted, show all active
            $expertises = ExpertiseManager::activeExpertise()->get();
        }

        $focusAreas  = FocusAreaManager::activeFocusArea()->get();
        $clientObjectives = ClientObjective::with(['client', 'objective_manager'])->get();

        $consultingData = null;
        $taskId = null;

        if ($id !== 'new') {
            $consultingData = Consulting::with([
                'client_objective',
                'expertise_manager',
                'focus_area_manager'
            ])->findOrFail($id);

            $task = Task::where('client_objective_id', $consultingData->client_objective_id)
                ->first();

            if ($task) {
                $taskId = $task->id;
            }

            $html =  view('admin.consulting.consulting-modal', compact('consultingData', 'clientObjectives', 'expertises', 'focusAreas', 'taskId'))->render();
        } else {
            $html =  view('admin.consulting.consulting-modal', compact('clientObjectives', 'expertises', 'focusAreas', 'taskId'))->render();
        }
        return response()->json(['success' => true, 'html' => $html], 200);
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
            'focus_area_manager_id' => [
                'required',
                'integer',
                'exists:focus_area_managers,id',
            ],
            'consulting_datetime' => [
                'required',
                'date',
                // 'after_or_equal:today',
            ],
        ], [
            'client_objective_id.required' => 'Please select a client objective.',
            'expertise_manager_id.required' => 'Please select an expertise.',
            'focus_area_manager_id.required' => 'Please select a focus area.',
            'consulting_datetime.required' => 'Please select consulting date & time.',
            // 'consulting_datetime.after_or_equal' => 'Consulting date cannot be in the past.',
        ]);

        try {
            // Start transaction

            $consulting = Consulting::findOrFail($id);
            $consulting->fill($data);
            $consulting->updated_by = auth()->id();
            $consulting->save();

            $focusArea = FocusAreaManager::find($data['focus_area_manager_id']);

            $taskId = $request->input('task_id');

            if ($taskId) {
                $task = $this->updateTaskFromConsulting($taskId, $consulting, $focusArea);
            } else {
                $task = $this->findOrCreateTaskFromConsulting($consulting, $focusArea);
            }

            // Add task_id to response
            $responseData = [
                'success' => true,
                'message' => 'Consulting updated successfully',
                'consulting_id' => $consulting->id,
                'task_id' => $task->id
            ];


            return response()->json($responseData, 200);
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
            // Start transaction

            $consulting = Consulting::findOrFail($id);

            // Find associated task and delete if exists
            $task = Task::where('client_objective_id', $consulting->client_objective_id)
                ->first();

            if ($task) {
                $task->delete();
            }

            // Delete consulting
            $consulting->delete();


            return response()->json(['success' => true, 'message' => 'Consulting deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    /**
     * Create a task from consulting data
     */
    private function createTaskFromConsulting(Consulting $consulting, $focusArea)
    {
        $task = new Task();
        $task->client_objective_id = $consulting->client_objective_id;
        $task->expertise_manager_id = $consulting->expertise_manager_id;
        $task->title = $focusArea ? $focusArea->name : 'Consulting Task';
        $task->task_start_date = Carbon::parse($consulting->consulting_datetime)->format('Y-m-d');
        $task->task_due_date = Carbon::parse($consulting->consulting_datetime)->format('Y-m-d');
        $pendingStatus = $this->getPendingStatus();
        if ($pendingStatus) {
            $task->status_manager_id = $pendingStatus->id;
        }
        $task->created_by = auth()->id();
        $task->save();

        return $task;
    }


    private function updateTaskFromConsulting($taskId, Consulting $consulting, $focusArea)
    {
        $task = Task::findOrFail($taskId);

        $task->client_objective_id = $consulting->client_objective_id;
        $task->expertise_manager_id = $consulting->expertise_manager_id;
        $task->title = $focusArea ? $focusArea->name : 'Consulting Task';
        $task->task_due_date = Carbon::parse($consulting->consulting_datetime)->format('Y-m-d');
        $task->updated_by = auth()->id();
        $task->save();

        return $task;
    }


    private function findOrCreateTaskFromConsulting(Consulting $consulting, $focusArea)
    {
        $task = Task::where('client_objective_id', $consulting->client_objective_id)
            ->first();

        if (!$task) {
            $task = new Task();
            $task->created_by = auth()->id();
            $pendingStatus = $this->getPendingStatus();
            if ($pendingStatus) {
                $task->status_manager_id = $pendingStatus->id;
            }
        }

        $task->client_objective_id = $consulting->client_objective_id;
        $task->expertise_manager_id = $consulting->expertise_manager_id;
        $task->title = $focusArea ? $focusArea->name : 'Consulting Task';
        $task->task_due_date = Carbon::parse($consulting->consulting_datetime)->format('Y-m-d');
        $task->updated_by = auth()->id();
        $task->save();

        return $task;
    }

    private function getPendingStatus()
    {
        $status = StatusManager::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%Pending%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%Pending%']);
        })
            ->activeStatus()
            ->first();

        if (!$status) {
            $status = StatusManager::activeStatus()->first();
        }

        return $status;
    }
}
