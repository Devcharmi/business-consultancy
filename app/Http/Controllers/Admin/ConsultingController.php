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
            $consulting = new Consulting();
            $consulting->fill($data);
            $consulting->created_by = auth()->id();
            $consulting->save();
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

        if ($id !== 'new') {
            $consultingData = Consulting::with([
                'client_objective',
                'expertise_manager',
                'focus_area_manager'
            ])->findOrFail($id);

            $html =  view('admin.consulting.consulting-modal', compact('consultingData', 'clientObjectives', 'expertises', 'focusAreas'))->render();
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
            $consulting = Consulting::findOrFail($id);

            $consulting->fill($data);
            $consulting->updated_by = auth()->id();
            $consulting->save();
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
            $consulting = Consulting::findOrFail($id);
            $consulting->delete();
            return response()->json(['success' => true, 'message' => 'Consulting deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
