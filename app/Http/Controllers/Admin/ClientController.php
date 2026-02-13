<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Consulting;
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

    public function clientConsultings(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
        ]);

        $user = auth()->user();
        $clientId = $request->client_id;

        $clientData = Client::findOrFail($clientId);
        $clientName = $clientData->client_name;

        $consultings = Consulting::with([
            'expertise_manager',
            'client_objective.client',
            'client_objective.objective_manager',
        ])
            ->whereHas('client_objective', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })
            ->accessibleBy($user)
            ->orderBy('consulting_date')
            ->get();

        return view('admin.day-consulting-modal', compact(
            'consultings',
            'clientId',
            'clientName'
        ))->render();
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
