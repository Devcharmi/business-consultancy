<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:client.allow')->only(['index', 'show']);
        $this->middleware('permission:client.create')->only(['store']);
        $this->middleware('permission:client.edit')->only(['update']);
        $this->middleware('permission:client.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $columns = ['id', 'client_name', 'email', 'phone', 'status'];

            $tableData = Client::Filters($data, $columns)
                ->select($columns);

            unset($data['start']);
            unset($data['length']);

            $tableDataCount = Client::Filters($data, $columns)->count();

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.client.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($id != 'new') {
            $clientData = Client::find($id);
            return view('admin.client.client-form', ['clientData' => $clientData]);
        } else {
            return view('admin.client.client-form');
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'client_name' => 'required',
            'gst_number' => [
                'nullable', // or 'required' if mandatory
                'regex:/^([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1})$/',
            ],
        ]);
        try {
            $client = new Client();
            $client->fill($data);
            $client->created_by = auth()->id();
            $client->save();
            return response()->json(['success' => true, 'message' => 'Client created successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $request->validate([
            'client_name' => 'required',
            'gst_number' => [
                'nullable', // or 'required' if mandatory
                'regex:/^([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1})$/',
            ],
        ]);
        try {
            $client = Client::find($id);
            $client->fill($data);
            $client->updated_by = auth()->id();
            $client->save();
            return response()->json(['success' => true, 'message' => 'Client updated successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();
            return response()->json(['success' => true, 'message' => 'Client deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
