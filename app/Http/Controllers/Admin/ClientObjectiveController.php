<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientObjective;
use App\Models\ExpertiseManager;
use App\Models\ObjectiveManager;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientObjectiveController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:client-objective.allow')->only(['index', 'show']);
        $this->middleware('permission:client-objective.create')->only(['store']);
        $this->middleware('permission:client-objective.edit')->only(['update']);
        $this->middleware('permission:client-objective.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'client_id', 'objective_manager_id'];

            $tableData = ClientObjective::Filters($data, $columns)
                ->select($columns);

            unset($data['start']);
            unset($data['length']);

            $tableDataCount = ClientObjective::Filters($data, $columns)->count();

            $tableData = $tableData->with(['client', 'objective_manager'])->get();

            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.client_objective.index');
    }

    // public function getObjectiveDetails(Request $request, $id)
    // {
    //     $clientObjective = ClientObjective::with(['client', 'objective_manager'])
    //         ->findOrFail($id);

    //     // ALWAYS a collection
    //     $expertiseManagers = ExpertiseManager::activeExpertise()->get();

    //     if ($expertiseManagers->isEmpty()) {
    //         return response()->json([
    //             'success' => true,
    //             'html' => '<div class="p-4 text-center text-muted">No expertise available</div>',
    //             'activeExpertiseId' => null
    //         ]);
    //     }

    //     $activeExpertiseId = $request->expertise_manager_id
    //         ?? $expertiseManagers->first()->id;

    //     $tasks = Task::where('client_objective_id', $id)
    //         ->where('expertise_manager_id', $activeExpertiseId)
    //         ->with(['status_manager'])
    //         ->get();

    //     $expertiseManagers = $expertiseManagers->map(function ($expertise) use ($tasks, $activeExpertiseId) {
    //         $expertise->tasks = $expertise->id == $activeExpertiseId
    //             ? $tasks
    //             : collect();

    //         $expertise->total_tasks = $expertise->tasks->count();
    //         $expertise->is_active = $expertise->id == $activeExpertiseId;

    //         return $expertise;
    //     });

    //     $html = view(
    //         'admin.client_objective.objective-details',
    //         compact('clientObjective', 'expertiseManagers')
    //     )->render();

    //     return response()->json([
    //         'success' => true,
    //         'html' => $html,
    //         'activeExpertiseId' => $activeExpertiseId
    //     ]);
    // }

public function getObjectiveDetails(Request $request, $id)
{
    $clientObjective = ClientObjective::with(['client', 'objective_manager'])
        ->findOrFail($id);

    $expertiseManagers = ExpertiseManager::activeExpertise()->get();

    // 🔥 active expertise (from request OR first)
    $activeExpertiseId = $request->expertise_manager_id
        ?? $expertiseManagers->first()?->id;

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ GET TOTAL COUNTS (ALL expertise)
    |--------------------------------------------------------------------------
    */
    $taskCounts = Task::where('client_objective_id', $id)
        ->selectRaw('expertise_manager_id, COUNT(*) as total')
        ->groupBy('expertise_manager_id')
        ->pluck('total', 'expertise_manager_id');

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ GET TASKS FOR ACTIVE EXPERTISE ONLY
    |--------------------------------------------------------------------------
    */
    $activeTasks = Task::where('client_objective_id', $id)
        ->where('expertise_manager_id', $activeExpertiseId)
        ->with(['content', 'status_manager'])
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ MAP DATA TO EXPERTISE
    |--------------------------------------------------------------------------
    */
    $expertiseManagers = $expertiseManagers->map(function ($expertise) use (
        $taskCounts,
        $activeExpertiseId,
        $activeTasks
    ) {
        $expertise->total_tasks = $taskCounts[$expertise->id] ?? 0;

        $expertise->tasks = $expertise->id == $activeExpertiseId
            ? $activeTasks
            : collect();

        $expertise->is_active = $expertise->id == $activeExpertiseId;

        return $expertise;
    });

    $html = view(
        'admin.client_objective.objective-details',
        compact('clientObjective', 'expertiseManagers')
    )->render();

    return response()->json([
        'success' => true,
        'html' => $html,
        'activeExpertiseId' => $activeExpertiseId
    ]);
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
            'client_id' => 'required',
            'objective_manager_id' => 'required',
        ]);
        try {
            $clientObjective = new ClientObjective();
            $clientObjective->fill($data);
            $clientObjective->save();
            return response()->json(['success' => true, 'message' => 'Client Objective created successfully'], 200);
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
        $clients  = Client::activeClients()->get();
        $objectives  = ObjectiveManager::activeObjectives()->get();

        if ($id !== 'new') {
            $ClientObjectiveData = ClientObjective::with([
                'client',
                'objective_manager'
            ])->findOrFail($id);

            $html =  view('admin.client_objective.client-objective-modal', compact('ClientObjectiveData', 'clients', 'objectives'))->render();
        } else {

            $html =  view('admin.client_objective.client-objective-modal', compact('clients', 'objectives'))->render();
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
            'client_id' => 'required',
            'objective_manager_id' => 'required',
        ]);
        try {
            $clientObjective = ClientObjective::find($id);
            $clientObjective->fill($data);
            $clientObjective->save();
            return response()->json(['success' => true, 'message' => 'Client Objective updated successfully'], 200);
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
            $clientObjective = ClientObjective::findOrFail($id);
            $clientObjective->delete();
            return response()->json(['success' => true, 'message' => 'Client Objective deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
