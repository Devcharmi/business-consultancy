<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ConsultingSampleExport;
use App\Http\Controllers\Controller;
use App\Imports\ConsultingImport;
use App\Models\Client;
use App\Models\ClientObjective;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use App\Models\FocusAreaManager;
use App\Models\ObjectiveManager;
use App\Models\StatusManager;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
                // 'client_id',
                // 'objective_manager_id',
                'client_objective_id',
                'expertise_manager_id',
                'focus_area_manager_id',
                'consulting_date',
                'start_time',
                'end_time',
            ];

            $tableData = Consulting::query()
                ->accessibleBy(auth()->user())   // 👈 ADD HERE
                ->filters($data, $columns)
                ->select($columns);

            unset($data['start']);
            unset($data['length']);

            $tableDataCount = Consulting::query()
                ->accessibleBy(auth()->user())   // 👈 ADD HERE ALSO
                ->filters($data, $columns)
                ->count();

            $tableData = $tableData->with([
                'client_objective.client',
                'client_objective.objective_manager',
                'expertise_manager',
                'focus_area_manager'
            ])->get();


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

    protected function getOrCreateClientObjective(int $clientId, int $objectiveManagerId): ClientObjective
    {
        return ClientObjective::firstOrCreate(
            [
                'client_id' => $clientId,
                'objective_manager_id' => $objectiveManagerId,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validated = $request->validate([
            // 'client_objective_id' => [
            //     'required',
            //     'integer',
            //     'exists:client_objectives,id',
            // ],
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'objective_manager_id' => [
                'required',
                'integer',
                'exists:objective_managers,id',
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
            'consulting_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            // Optional custom messages
            // 'client_objective_id.required' => 'Please select a client objective.',
            'client_id.required' => 'Please select a client.',
            'objective_manager_id.required' => 'Please select a objective.',
            'expertise_manager_id.required' => 'Please select an expertise.',
            'focus_area_manager_id.required' => 'Please select a focus area.',
            'consulting_date.required' => 'Please select consulting date & time.',
            'start_time.required' => 'Please select start time.',
            'end_time.required' => 'Please select end time.',
            'end_time.after' => 'End time must be greater than start time.',
            // 'consulting_date.after_or_equal' => 'Consulting date must be today or future.',
        ]);

        $overlap = Consulting::hasTimeOverlap(
            $validated['consulting_date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['expertise_manager_id']
        );

        if ($overlap) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'start_time' => ['This time slot overlaps with an existing consulting.']
                ]
            ], 422);
        }

        try {
            // Start transaction
            DB::beginTransaction();
            // ✅ Get or create client objective
            $clientObjective = $this->getOrCreateClientObjective(
                $validated['client_id'],
                $validated['objective_manager_id']
            );
            // Create consulting
            $consulting = new Consulting();
            $consulting->fill($data);
            $consulting->client_objective_id = $clientObjective->id;
            $consulting->created_by = auth()->id();
            $consulting->save();

            // $focusArea = FocusAreaManager::find($data['focus_area_manager_id']);

            // $task = $this->createTaskFromConsulting($consulting, $focusArea);

            $responseData = [
                'success' => true,
                'message' => 'Consulting created successfully',
                'consulting_id' => $consulting->id,
                // 'task_id' => $task->id
            ];
            DB::commit();

            return response()->json($responseData, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
            ], 500);
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
        // $clientObjectives = ClientObjective::with(['client', 'objective_manager'])->get();
        $clients = Client::select('id', 'client_name')->activeClients()->orderBy('client_name')->get();
        $objectives = ObjectiveManager::activeObjectives()->select('id', 'name')->orderBy('name')->get();

        $consultingData = null;
        $taskId = null;

        if ($id !== 'new') {
            $consultingData = Consulting::with([
                'client_objective.client',
                'client_objective.objective_manager',
                'expertise_manager',
                'focus_area_manager'
            ])->findOrFail($id);

            $task = Task::where('client_objective_id', $consultingData->client_objective_id)
                ->first();

            $taskId = $task?->id;
        }

        $html = view(
            'admin.consulting.consulting-modal',
            compact(
                'consultingData',
                'clients',
                'objectives',
                'expertises',
                'focusAreas',
                'taskId'
            )
        )->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ], 200);
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
        $validated = $request->validate([
            // 'client_objective_id' => [
            //     'required',
            //     'integer',
            //     'exists:client_objectives,id',
            // ],
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'objective_manager_id' => [
                'required',
                'integer',
                'exists:objective_managers,id',
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
            'consulting_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            // 'client_objective_id.required' => 'Please select a client objective.',
            'client_id.required' => 'Please select a client.',
            'objective_manager_id.required' => 'Please select a objective.',
            'expertise_manager_id.required' => 'Please select an expertise.',
            'focus_area_manager_id.required' => 'Please select a focus area.',
            'consulting_date.required' => 'Please select consulting date & time.',
            'start_time.required' => 'Please select start time.',
            'end_time.required' => 'Please select end time.',
            'end_time.after' => 'End time must be greater than start time.',
            // 'consulting_date.after_or_equal' => 'Consulting date cannot be in the past.',
        ]);

        $overlap = Consulting::hasTimeOverlap(
            $validated['consulting_date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['expertise_manager_id'],
            $id // ignore current record
        );

        if ($overlap) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'start_time' => ['This time slot overlaps with an existing consulting.']
                ]
            ], 422);
        }

        try {
            // Start transaction
            DB::beginTransaction();
            // ✅ Get or create client objective
            $clientObjective = $this->getOrCreateClientObjective(
                $validated['client_id'],
                $validated['objective_manager_id']
            );

            $consulting = Consulting::findOrFail($id);
            $consulting->fill($data);
            $consulting->client_objective_id = $clientObjective->id;
            $consulting->updated_by = auth()->id();
            $consulting->save();

            // $focusArea = FocusAreaManager::find($data['focus_area_manager_id']);

            // $taskId = $request->input('task_id');

            // if ($taskId) {
            //     $task = $this->updateTaskFromConsulting($taskId, $consulting, $focusArea);
            // } else {
            //     $task = $this->findOrCreateTaskFromConsulting($consulting, $focusArea);
            // }

            // Add task_id to response
            $responseData = [
                'success' => true,
                'message' => 'Consulting updated successfully',
            ];

            DB::commit();


            return response()->json($responseData, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
            ], 500);
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
        $task->task_start_date = Carbon::parse($consulting->consulting_date)->format('Y-m-d');
        $task->task_due_date = Carbon::parse($consulting->consulting_date)->format('Y-m-d');
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
        $task->task_start_date = Carbon::parse($consulting->consulting_date)->format('Y-m-d');
        $task->task_due_date = Carbon::parse($consulting->consulting_date)->format('Y-m-d');
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
        $task->task_due_date = Carbon::parse($consulting->consulting_date)->format('Y-m-d');
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

    public function downloadSample()
    {
        return Excel::download(new ConsultingSampleExport, 'consulting_sample_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $import = new ConsultingImport;

        Excel::import($import, $request->file('file'));

        return response()->json([
            'success' => $import->importedCount > 0,
            'importedCount' => $import->importedCount,
            'message' => $import->importedCount . ' rows imported successfully.',
            'errors'  => $import->errors ?? [],
        ]);
    }

    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,csv'
    //     ]);

    //     $import = new ConsultingImport;

    //     Excel::import($import, $request->file('file'));

    //     return response()->json([
    //         'success' => $import->importedCount > 0,   // true if any rows imported
    //         'message' => $import->importedCount . ' rows imported successfully.',
    //         'errors'  => $import->errors ?? [],        // always return array
    //     ]);
    // }
}
