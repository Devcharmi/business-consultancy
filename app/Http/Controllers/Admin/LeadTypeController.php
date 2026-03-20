<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:lead-type.allow')->only(['index', 'show']);
        $this->middleware('permission:lead-type.create')->only(['store']);
        $this->middleware('permission:lead-type.edit')->only(['update']);
        $this->middleware('permission:lead-type.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'status'];

            $tableData = LeadType::Filters($data, $columns)
                ->select($columns);


            unset($data['start']);
            unset($data['length']);

            $tableDataCount = LeadType::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.lead_type.index');
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
            'name' => 'required|unique:leads,name'
        ]);
        try {
            $lead = new LeadType();
            $lead->fill($data);
            $lead->save();
            return response()->json(['success' => true, 'message' => 'Lead Type created successfully'], 200);
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
            $leadTypeData = LeadType::find($id);
            $html = view('admin.lead_type.lead-type-modal', ['leadTypeData' => $leadTypeData])->render();
        } else {
            $html = view('admin.lead_type.lead-type-modal')->render();
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
            'name' => 'required|unique:leads,name,' . $id,
        ]);
        try {
            $lead = LeadType::find($id);
            $lead->fill($data);
            $lead->save();
            return response()->json(['success' => true, 'message' => 'Lead Type updated successfully'], 200);
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
            $lead = LeadType::findOrFail($id);
            $lead->delete();
            return response()->json(['success' => true, 'message' => 'Lead Type deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
