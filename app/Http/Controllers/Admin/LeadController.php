<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadFollowUp;
use App\Models\ObjectiveManager;
use App\Models\User;
use App\Models\VendorService;
use App\Services\LeadConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->all();

            $columns = [
                'id',
                'name',
                'email',
                'phone',
                'objective_manager_id',
                'user_id',
                'status',
                'created_at',
            ];

            // $tableData = Lead::with(['service', 'vendor', 'followUps'])
            //     ->when(($data['filter'] ?? null) === 'open_follow_up', function ($q) {
            //         $q->openFollowUps();
            //     })->Filters($data, $columns)
            //     ->select($columns);

            $tableData = Lead::with([
                'objective_manager:id,name',
                'user:id,name',
                'followUps'
            ])
                ->when(($data['filter'] ?? null) === 'open_follow_up', function ($q) {
                    $q->openFollowUps();
                })
                ->Filters($data, $columns);


            unset($data['start'], $data['length']);

            // $tableDataCount = Lead::Filters($data, $columns)->count();
            $tableDataCount = Lead::query()
                ->Filters($data, $columns)
                ->count();

            $tableData = $tableData->get();
            // dd($tableData);

            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords']        = $tableDataCount;
            $response['draw']                 = intval(collect($data)->get('draw'));
            $response['aaData']               = $tableData->toArray();

            return $response;
        }

        return view('admin.leads.index');
    }

    public function show($id)
    {
        $clients  = Client::activeClients();
        $objectives  = ObjectiveManager::activeObjectives();

        if ($id !== 'new') {
            $leadData = Lead::with([
                'client',
                'objective_manager',
                'followUps',
            ])->findOrFail($id);

            return view('admin.leads.lead-form', compact('leadData', 'clients', 'objectives'));
        }

        return view('admin.leads.lead-form', compact('clients', 'objectives'));
    }

    public function store(Request $request)
    {
        // ✅ SINGLE validation block
        $validated = $request->validate([
            'objective_manager_id' => 'required|exists:objective_managers,id',

            'client_id' => 'nullable|exists:clients,id',

            'name'  => 'required|string|max:255',

            'phone' => [
                'required',
                'string',
                'max:20',

                // ✅ Only check uniqueness when client NOT selected
                Rule::unique('clients', 'phone')
                    ->when(!$request->client_id, function ($query) {
                        return $query;
                    }),
            ],

            'email' => [
                'nullable',
                'email',

                Rule::unique('clients', 'email')
                    ->when(!$request->client_id, function ($query) {
                        return $query;
                    }),
            ],

            'status' => 'required|in:new,contacted,converted,lost',
            'note'   => 'nullable|string',
        ]);

        try {

            $validated['user_id'] = Auth::id();

            $lead = Lead::create($validated);

            LeadConversionService::convertIfRequired(
                $lead,
                $request->status
            );

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully'
            ], 200);
        } catch (\Throwable $th) {

            Log::error('Lead Store Error: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    public function update(Request $request, Lead $lead)
    {

        // ✅ SINGLE validation block
        $validated = $request->validate([
            'objective_manager_id' => 'required',
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'email'      => 'nullable|email',
            'status'     => 'required|in:new,contacted,converted,lost',
            'note'       => 'nullable|string',
        ]);

        try {

            $lead->update($validated);

            LeadConversionService::convertIfRequired(
                $lead,
                $request->status
            );

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully'
            ], 200);
        } catch (\Throwable $th) {

            Log::error('Lead Update Error: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $lead->delete();
            return response()->json(['success' => true, 'message' => 'Lead deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function followupStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'remark' => 'required|string',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        // ❌ Prevent follow-ups on closed leads
        if (in_array($lead->status, ['converted', 'lost'])) {
            return response()->json([
                'message' => 'This lead is already closed.'
            ], 422);
        }

        // ✅ Save follow-up
        LeadFollowUp::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'remark' => $request->remark,
            'next_follow_up_at' => $request->next_follow_up_at,
        ]);

        return response()->json([
            'message' => 'Follow up added successfully'
        ]);
    }

    public function followUpsList(Lead $lead)
    {
        $followUps = $lead->followUps()
            ->latest()
            ->get();

        return view(
            'admin.leads.followups-list',
            compact('lead', 'followUps')
        );
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status'  => 'required|in:new,contacted,converted,lost'
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        LeadConversionService::convertIfRequired(
            $lead,
            $request->status
        );

        $lead->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
