<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatusManagerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:status manager.allow')->only(['index', 'show']);
        $this->middleware('permission:status manager.create')->only(['store']);
        $this->middleware('permission:status manager.edit')->only(['update']);
        $this->middleware('permission:status manager.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'color_name', 'status'];

            $tableData = StatusManager::Filters($data, $columns)
                ->select($columns);


            unset($data['start']);
            unset($data['length']);

            $tableDataCount = StatusManager::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.status_manager.index');
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
            'name' => 'required|unique:status_managers,name'
        ]);
        try {
            $status = new StatusManager();
            $status->fill($data);
            $status->save();
            return response()->json(['success' => true, 'message' => 'Status created successfully'], 200);
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
            $statusData = StatusManager::find($id);
            $html = view('admin.status_manager.status-modal', ['statusData' => $statusData])->render();
        } else {
            $html = view('admin.status_manager.status-modal')->render();
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
            'name' => 'required|unique:status_managers,name,' . $id,
        ]);
        try {
            $status = StatusManager::find($id);
            $status->fill($data);
            $status->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully'], 200);
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
            $status = StatusManager::findOrFail($id);
            $status->delete();
            return response()->json(['success' => true, 'message' => 'Status deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
