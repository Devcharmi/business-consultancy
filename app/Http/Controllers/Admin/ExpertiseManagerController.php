<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\expertiseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpertiseManagerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:expertise.allow')->only(['index', 'show']);
        $this->middleware('permission:expertise.create')->only(['store']);
        $this->middleware('permission:expertise.edit')->only(['update']);
        $this->middleware('permission:expertise.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'color_name', 'status'];

            $tableData = ExpertiseManager::Filters($data, $columns)
                ->select($columns);


            unset($data['start']);
            unset($data['length']);

            $tableDataCount = ExpertiseManager::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.expertise_manager.index');
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
            'name' => 'required|unique:expertise_managers,name'
        ]);
        try {
            $expertise = new expertiseManager();
            $expertise->fill($data);
            $expertise->save();
            return response()->json(['success' => true, 'message' => 'Expertise created successfully'], 200);
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
            $expertiseData = ExpertiseManager::find($id);
            $html = view('admin.expertise_manager.expertise-modal', ['expertiseData' => $expertiseData])->render();
        } else {
            $html = view('admin.expertise_manager.expertise-modal')->render();
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
            'name' => 'required|unique:expertise_managers,name,' . $id,
        ]);
        try {
            $expertise = ExpertiseManager::find($id);
            $expertise->fill($data);
            $expertise->save();
            return response()->json(['success' => true, 'message' => 'Expertise updated successfully'], 200);
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
            $expertise = ExpertiseManager::findOrFail($id);
            $expertise->delete();
            return response()->json(['success' => true, 'message' => 'Expertise deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
