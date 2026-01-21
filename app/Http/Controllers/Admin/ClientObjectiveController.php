<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientObjective;
use App\Models\ObjectiveManager;
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
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.client_objective.index');
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
