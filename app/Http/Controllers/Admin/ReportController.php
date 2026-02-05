<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Consulting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function marketplaceDemand()
    {
        return view('admin.reports.marketplace-demand');
    }


    public function consulting()
    {
        return view('admin.reports.consulting');
    }

    public function consultingData(Request $request)
    {
        $query = Consulting::with([
            'client_objective.client',
            'expertise_manager',
            'focus_area_manager',
        ]);

        // 🔹 Date range
        if ($request->date_range) {
            [$from, $to] = explode(' - ', $request->date_range);
            $query->whereBetween('consulting_datetime', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('client', fn($r) => $r->client_objective->client->client_name ?? '-')
            ->addColumn('objective', fn($r) => $r->client_objective->objective_manager->name ?? '-')
            ->addColumn('expertise', fn($r) => $r->expertise_manager->name ?? '-')
            ->addColumn('focus_area', fn($r) => $r->focus_area_manager->name ?? '-')
            ->addColumn('date', fn($r) => $r->consulting_datetime->format('d-m-Y'))
            ->rawColumns([])
            ->make(true);
    }

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
                        'status'       => $client->status ? 'Active' : 'Inactive',
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
}
