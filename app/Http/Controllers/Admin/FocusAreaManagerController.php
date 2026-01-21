<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FocusAreaManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FocusAreaManagerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:focus-area.allow')->only(['index', 'show']);
        $this->middleware('permission:focus-area.create')->only(['store']);
        $this->middleware('permission:focus-area.edit')->only(['update']);
        $this->middleware('permission:focus-area.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'status'];

            $tableData = FocusAreaManager::Filters($data, $columns)
                ->select($columns);


            unset($data['start']);
            unset($data['length']);

            $tableDataCount = FocusAreaManager::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.focus_area_manager.index');
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
            'name' => 'required|unique:focus_area_managers,name'
        ]);
        try {
            $focusArea = new FocusAreaManager();
            $focusArea->fill($data);
            $focusArea->save();
            return response()->json(['success' => true, 'message' => 'Focus Area created successfully'], 200);
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
            $focusAreaData = FocusAreaManager::find($id);
            $html = view('admin.focus_area_manager.focus-area-modal', ['focusAreaData' => $focusAreaData])->render();
        } else {
            $html = view('admin.focus_area_manager.focus-area-modal')->render();
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
            'name' => 'required|unique:focus_area_managers,name,' . $id,
        ]);
        try {
            $focusArea = FocusAreaManager::find($id);
            $focusArea->fill($data);
            $focusArea->save();
            return response()->json(['success' => true, 'message' => 'Focus Area updated successfully'], 200);
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
            $focusArea = FocusAreaManager::findOrFail($id);
            $focusArea->delete();
            return response()->json(['success' => true, 'message' => 'Focus Area deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
