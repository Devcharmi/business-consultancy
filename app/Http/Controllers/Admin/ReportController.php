<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientObjective;
use App\Models\Consulting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function clientIndex(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $data['date_range']     = $request->get('dateRange');
            $data['filterClient']    = $request->get('filterClient');
            $data['filterCreatedBy'] = $request->get('filterCreatedBy');

            $columns = [
                0 => 'client_name',
                1 => 'contact_person',
                2 => 'email',
                3 => 'phone',
                4 => 'status',
                5 => 'created_by',
                6 => 'updated_by',
                7 => 'client_objectives_count',
                8 => 'client_consultings_count',
                9 => 'client_meetings_count',
            ];

            $query = $query = Client::withCount([
                'clientObjectives',
                'consultings as client_consultings_count',
                'meetings as client_meetings_count',
            ])
                ->with(['createdBy:id,name', 'updatedBy:id,name']);

            // 🔹 Apply your existing filter scope
            $query->filters($data, $columns);

            $totalRecords = Client::count();
            $filteredRecords = $query->count();

            $clients = $query->get();

            return response()->json([
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $clients->map(function ($client) {
                    return [
                        'client_name'  => $client->client_name,
                        'email'        => $client->email,
                        'phone'        => $client->phone,
                        'status'       => $client->status == 1 ? 'Active' : 'Inactive',
                        'created_by'   => optional($client->createdBy)->name,
                        // 'updated_by'   => optional($client->updatedBy)->name,
                        'objectives'   => $client->client_objectives_count,
                        'consultings'   => $client->client_consultings_count,
                        'meetings'   => $client->client_meetings_count,
                    ];
                }),
            ]);
        }
        $filterRouteConfig = config('filter.route_filters');

        $filters = filterDropdowns(array_keys($filterRouteConfig));
        return view('admin.reports.client', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }

    public function objectiveIndex(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->all();
            $data['date_range']     = $request->get('dateRange');
            $data['filterClient']   = $request->get('filterClient');
            $data['filterCreatedBy'] = $request->get('filterCreatedBy');
            $data['filterObjective']   = $request->get('filterObjective');

            $columns = [
                0 => 'client_id',
                1 => 'objective_manager_id',
                3 => 'consultings_count',
                4 => 'meetings_count',
                6 => 'created_by',
            ];

            $query = ClientObjective::with([
                'client:id,client_name',
                'objective_manager:id,name',
                'createdBy:id,name',
            ])
                ->withCount([
                    'consultings',
                    'meetings',
                ]);

            // 🔹 same reusable filter scope
            $query->filters($data, $columns);

            $totalRecords = ClientObjective::count();
            $filteredRecords = $query->count();

            $objectives = $query->get();

            return response()->json([
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $objectives->map(function ($obj) {
                    return [
                        'client'      => optional($obj->client)->client_name,
                        'objective'   => optional($obj->objective_manager)->name,
                        'consultings' => $obj->consultings_count,
                        'meetings'    => $obj->meetings_count,
                        'created_by'  => optional($obj->createdBy)->name,
                    ];
                }),
            ]);
        }

        // 🔹 Filters (same pattern)
        $filterRouteConfig = config('filter.route_filters');
        $filters = filterDropdowns(array_keys($filterRouteConfig));

        return view('admin.reports.objective', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }

    public function consultingIndex(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->all();
            $data['date_range']     = $request->get('dateRange');
            $data['filterClient']   = $request->get('filterClient');
            $data['filterObjective'] = $request->get('filterObjective');
            $data['filterExpertise'] = $request->get('filterExpertise');

            $columns = [
                0 => 'client_objective_id',
                1 => 'expertise_manager_id',
                2 => 'total_count',
                3 => 'last_date',
            ];

            $query = Consulting::select(
                'client_objective_id',
                'expertise_manager_id'
            )
                ->with([
                    'client_objective.client:id,client_name',
                    'client_objective.objective_manager:id,name',
                    'expertise_manager:id,name,color_name',
                ])
                ->selectRaw('COUNT(*) as total_count')
                ->selectRaw('MAX(consulting_datetime) as last_date')
                ->groupBy('client_objective_id', 'expertise_manager_id');

            // 🔹 Uses YOUR scopeFilters()
            $query->filters($data, $columns);

            $totalRecords = $query->count();
            $records = $query->get();

            return response()->json([
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $records->map(function ($row) {
                    return [
                        'client'    => optional($row->client_objective->client)->client_name,
                        'objective' => optional($row->client_objective->objective_manager)->name,

                        'expertise' => [
                            'name'  => optional($row->expertise_manager)->name,
                            'color_name' => optional($row->expertise_manager)->color_name, // 👈 important
                        ],

                        'total'     => $row->total_count,
                        'last_date' => $row->last_date
                            ? Carbon::parse($row->last_date)->format('d-m-Y')
                            : '-',
                    ];
                }),
            ]);
        }

        $filterRouteConfig = config('filter.route_filters');
        $filters = filterDropdowns(array_keys($filterRouteConfig));

        return view('admin.reports.consulting', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }
}
