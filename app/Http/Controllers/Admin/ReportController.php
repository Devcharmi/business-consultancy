<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientObjective;
use App\Models\Consulting;
use App\Models\Lead;
use App\Models\UserTask;
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
                ->selectRaw('MAX(consulting_date) as last_date')
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

    public function leadIndex(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->all();
            $data['date_range'] = $request->get('dateRange');
            $data['filterClient'] = $request->get('filterClient');
            $data['filterCreatedBy']   = $request->get('filterCreatedBy');

            $columns = [
                4 => 'client_id',
                1 => 'email',
                2 => 'phone',
                3 => 'status',
                5 => 'user_id',
                6 => 'followups_count',
                7 => 'pending_followups_count',
                8 => 'last_followup_date',
            ];

            $query = Lead::with([
                'client:id,client_name',
                'user:id,name',
            ])
                ->withCount([
                    'followUps',
                    'userTasks',
                    'followUps as pending_followups_count' => function ($q) {
                        $q->where('status', 'pending');
                    },
                ])
                ->withMax('followUps', 'next_follow_up_at');

            // 🔹 Apply existing scopeFilters
            $query->filters($data, $columns);

            // 🔹 Extra filters
            if (!empty($data['filterClient'])) {
                $query->where('client_id', $data['filterClient']);
            }

            if (!empty($data['filterUser'])) {
                $query->where('user_id', $data['filterUser']);
            }

            if (!empty($data['filterStatus'])) {
                $query->where('status', $data['filterStatus']);
            }

            $totalRecords    = Lead::count();
            $filteredRecords = $query->count();
            $leads           = $query->get();

            return response()->json([
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $leads->map(function ($lead) {
                    return [
                        'client'     => optional($lead->client)->client_name ?? $lead->name,
                        'email'      => $lead->email,
                        'phone'      => $lead->phone,
                        'status'     => ucfirst($lead->status),
                        'assigned_to' => optional($lead->user)->name,
                        'followups'  => $lead->follow_ups_count,
                        'tasks'  => $lead->user_tasks_count,
                        'pending'    => $lead->pending_followups_count,
                        'next_followup' => $lead->follow_ups_max_next_follow_up_at
                            ? \Carbon\Carbon::parse($lead->follow_ups_max_next_follow_up_at)->format('d-m-Y')
                            : '-',
                    ];
                }),
            ]);
        }

        $filterRouteConfig = config('filter.route_filters');
        $filters = filterDropdowns(array_keys($filterRouteConfig));

        return view('admin.reports.leads', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }

    public function userTaskReport(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->all();
            $data['date_range']     = $request->get('dateRange');
            $data['filterStaff']    = $request->get('filterStaff');
            $data['filterClient']   = $request->get('filterClient');
            $data['filterStatus']   = $request->get('filterStatus');
            $data['filterPriority'] = $request->get('filterPriority');
            $data['filterEntity']   = $request->get('filterEntity');
            $data['filterTaskType'] = $request->get('filterTaskType');
            $data['tab']            = $request->get('tab');

            $columns = [
                0 => 'task_name',
                1 => 'entity_type',
                2 => 'task_type',
                3 => 'task_start_date',
                4 => 'task_due_date',
                5 => 'priority_manager_id',
                6 => 'status_manager_id',
                7 => 'staff_manager_id',
            ];

            $query = UserTask::with([
                'clients:id,client_name',
                'lead:id,name',
                'staff:id,name',
                'priority_manager:id,name,color_name',
                'status_manager:id,name,color_name',
            ]);

            // 🔹 Apply tab filters
            if ($data['tab'] == 'tasks') {
                $query->tasks();
            }

            if ($data['tab'] == 'meetings') {
                $query->meetings();
            }

            if ($data['tab'] == 'completed') {
                $query->whereNotNull('completed_at');
            }

            if ($data['tab'] == 'pending') {
                $query->whereNull('completed_at');
            }

            if ($data['tab'] == 'overdue') {
                $query->whereNull('completed_at')
                    ->whereDate('task_due_date', '<', now());
            }

            $query->filters($data, $columns);

            $totalRecords    = UserTask::count();
            $filteredRecords = $query->count();
            $tasks           = $query->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $tasks->map(function ($task) {

                    $isOverdue = $task->task_due_date &&
                        !$task->completed_at &&
                        now()->gt($task->task_due_date);

                    return [
                        'task_name' => $task->task_name,

                        'entity' => $task->entity_type === 'lead'
                            ? 'Lead - ' . optional($task->lead)->name
                            : 'Client - ' . optional($task->clients)->client_name,

                        'task_type' => ucfirst($task->task_type),

                        'start_date' => optional($task->task_start_date)?->format('d-m-Y'),

                        'due_date' => optional($task->task_due_date)?->format('d-m-Y'),

                        'priority_name'  => optional($task->priority_manager)->name,
                        'priority_color' => optional($task->priority_manager)->color_name,

                        'status_name'  => optional($task->status_manager)->name,
                        'status_color' => optional($task->status_manager)->color_name,

                        'assigned_to' => optional($task->staff)->name,

                        'overdue' => $isOverdue ? '<span class="badge bg-danger">Yes</span>' : '-',
                    ];
                }),
            ]);
        }
        // 🔹 Filters (same pattern)
        $filterRouteConfig = config('filter.route_filters');
        $filters = filterDropdowns(array_keys($filterRouteConfig));

        return view('admin.reports.user-task', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }
}
