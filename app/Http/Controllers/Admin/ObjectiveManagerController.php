<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ObjectiveManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ObjectiveManagerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:objective manager.allow')->only(['index', 'show']);
        $this->middleware('permission:objective manager.create')->only(['store']);
        $this->middleware('permission:objective manager.edit')->only(['update']);
        $this->middleware('permission:objective manager.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'status'];

            $tableData = ObjectiveManager::Filters($data, $columns)
                ->select($columns);


            unset($data['start']);
            unset($data['length']);

            $tableDataCount = ObjectiveManager::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.objective_manager.index');
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
            'name' => 'required|unique:objective_managers,name'
        ]);
        try {
            $status = new ObjectiveManager();
            $status->fill($data);
            $status->save();
            return response()->json(['success' => true, 'message' => 'Objective created successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($id != 'new') {
            $objectiveData = ObjectiveManager::find($id);
            $html = view('admin.objective_manager.objective-modal', ['objectiveData' => $objectiveData])->render();
        } else {
            $html = view('admin.objective_manager.objective-modal')->render();
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
            'name' => 'required|unique:objective_managers,name,' . $id,
        ]);
        try {
            $status = ObjectiveManager::find($id);
            $status->fill($data);
            $status->save();
            return response()->json(['success' => true, 'message' => 'Objective updated successfully'], 200);
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
            $status = ObjectiveManager::findOrFail($id);
            $status->delete();
            return response()->json(['success' => true, 'message' => 'Objective deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
