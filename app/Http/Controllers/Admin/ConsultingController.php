<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientObjective;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use App\Models\FocusAreaManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsultingController extends Controller
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

            $tableData = $tableData->with(['client_objective', 'expertise_manager', 'focus_area_manager'])->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.consulting.index');
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
            $status = new Consulting();
            $status->fill($data);
            $status->save();
            return response()->json(['success' => true, 'message' => 'Consulting created successfully'], 200);
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
        $clientObjectives = ClientObjective::with(['client', 'objectiveManager'])
            ->get()
            ->map(function ($row) {
                return [
                    'id'   => $row->id,
                    'text' => $row->client->client_name . ' - ' . $row->objectiveManager->name,
                ];
            });

        $expertises  = ExpertiseManager::activeExpertise();
        $focusAreas  = FocusAreaManager::activeFocusArea();

        if ($id !== 'new') {
            $ConsultingData = Consulting::with([
                'client_objective',
                'expertise_manager',
                'focus_area_manager'
            ])->findOrFail($id);

            $html =  view('admin.consulting.consulting-modal', compact('ConsultingData', 'clientObjectives', 'expertises', 'focusAreas'))->render();
        } else {

            $html =  view('admin.consulting.consulting-modal', compact('clientObjectives', 'expertises', 'focusAreas'))->render();
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
            $status = Consulting::find($id);
            $status->fill($data);
            $status->save();
            return response()->json(['success' => true, 'message' => 'Consulting updated successfully'], 200);
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
            $status = Consulting::findOrFail($id);
            $status->delete();
            return response()->json(['success' => true, 'message' => 'Consulting deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
