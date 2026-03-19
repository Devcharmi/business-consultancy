<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsultingTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:consulting-type.allow')->only(['index', 'show']);
        $this->middleware('permission:consulting-type.create')->only(['store']);
        $this->middleware('permission:consulting-type.edit')->only(['update']);
        $this->middleware('permission:consulting-type.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'status'];

            $tableData = ConsultingType::Filters($data, $columns)
                ->select($columns);


            unset($data['start']);
            unset($data['length']);

            $tableDataCount = ConsultingType::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.consulting_type.index');
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
            'name' => 'required|unique:consulting_types,name'
        ]);
        try {
            $consulting_type = new ConsultingType();
            $consulting_type->fill($data);
            $consulting_type->save();
            return response()->json(['success' => true, 'message' => 'Consulting Type created successfully'], 200);
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
            $consultingTypeData = ConsultingType::find($id);
            $html = view('admin.consulting_type.consulting-type-modal', ['consultingTypeData' => $consultingTypeData])->render();
        } else {
            $html = view('admin.consulting_type.consulting-type-modal')->render();
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
            'name' => 'required|unique:consulting_types,name,' . $id,
        ]);
        try {
            $consulting_type = ConsultingType::find($id);
            $consulting_type->fill($data);
            $consulting_type->save();
            return response()->json(['success' => true, 'message' => 'Consulting Type updated successfully'], 200);
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
            $consulting_type = ConsultingType::findOrFail($id);
            $consulting_type->delete();
            return response()->json(['success' => true, 'message' => 'Consulting Type deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
