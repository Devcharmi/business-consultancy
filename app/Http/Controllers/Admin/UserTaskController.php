<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\Models\PriorityManager;
use App\Models\StatusManager;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-task.allow')->only(['index', 'show']);
        $this->middleware('permission:user-task.create')->only(['store']);
        $this->middleware('permission:user-task.edit')->only(['update']);
        $this->middleware('permission:user-task.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $columns = [
            'id',
            'staff_manager_id',
            'client_id',
            'task_name',
            'priority_manager_id',
            'task_start_date',
            'task_due_date',
            'status_manager_id',
            'created_by',
            'source_type',
            'entity_type',
            'task_type',
            'lead_id',
        ];

        if ($request->ajax()) {

            $data = $request->all();
            // dd($data);
            // Apply external filters
            $data['date_range']     = $request->get('dateRange');
            $data['filterClient']    = $request->get('filterClient');
            $data['filterStaff']  = $request->get('filterStaff');
            $data['filterCreatedBy'] = $request->get('filterCreatedBy');
            $data['filterStatus'] = $request->get('filterStatus');
            $data['filterPriority'] = $request->get('filterPriority');
            $data['filterEntity'] = $request->get('filterEntity');
            $data['filterTaskType'] = $request->get('filterTaskType');
            $data['filterSource'] = $request->get('filterSource');
            // 🔥 Lead from URL
            $data['filterLead']      = $request->get('filterLead');

            $status = $request->get('status', 'all');

            // Base filtered query (DataTables)
            $filteredQuery = UserTask::Filters($data, $columns)
                ->select($columns)
                ->with([
                    'staff',
                    'priority_manager',
                    'created_by',
                    'status_manager',
                    'clients',
                    'lead',
                ]);

            // Apply selected TAB status
            $this->applyTaskStatusFilter($filteredQuery, $status);

            // Remove paginate params for counting
            unset($data['start'], $data['length']);

            // Count after filters + status
            $totalFiltered = UserTask::Filters($data, $columns)
                ->tap(fn($q) => $this->applyTaskStatusFilter($q, $status))
                ->count();

            // Tab counts
            // $counts = $this->getTaskCounts();
            $counts = $this->getTaskCounts($data, $columns);
            $records = $filteredQuery->with([
                'clients',
                'staff',
                'status_manager',
                'priority_manager',
                'lead'
            ])->get();

            return response()->json([
                'draw' => intval($request->get('draw')),
                'iTotalRecords' => $totalFiltered,
                'iTotalDisplayRecords' => $totalFiltered,
                'aaData' => $records,
                'counts' => $counts,
            ]);
        }

        // Non AJAX
        // $filters = FilterDropdownService::get();
        $filterRouteConfig = config('filter.route_filters');

        $filters = filterDropdowns(array_keys($filterRouteConfig));

        return view('admin.user_task.index', array_merge(
            $filters,
            compact('filterRouteConfig')
        ));
    }

    private function applyTaskStatusFilter($query, $status)
    {
        switch ($status) {

            // case 'today':
            //     $query->whereDate('task_start_date', today());
            //     break;
            case 'today':
                $query->whereDate('task_start_date', today());
                break;

            case 'done':
                $query->whereHas('status_manager', function ($q) {
                    $q->where('name', 'Done');
                });
                break;

            case 'pending':
                $query->whereDate('task_start_date', '<=', today())
                    ->whereDate('task_due_date', '>=', today())
                    ->whereHas('status_manager', function ($q) {
                        $q->where('name', '!=', 'Done');
                    });
                break;

            case 'overdue':
                $query->whereDate('task_due_date', '<', today())
                    ->whereHas('status_manager', function ($q) {
                        $q->where('name', '!=', 'Done');
                    });
                break;
        }
    }

    private function getTaskCounts($data, $columns)
    {
        return [
            'all' => UserTask::Filters($data, $columns)
                ->tap(fn($q) => $this->applyTaskStatusFilter($q, 'all'))
                ->count(),

            'today' => UserTask::Filters($data, $columns)
                ->tap(fn($q) => $this->applyTaskStatusFilter($q, 'today'))
                ->count(),

            'done' => UserTask::Filters($data, $columns)
                ->tap(fn($q) => $this->applyTaskStatusFilter($q, 'done'))
                ->count(),

            'pending' => UserTask::Filters($data, $columns)
                ->tap(fn($q) => $this->applyTaskStatusFilter($q, 'pending'))
                ->count(),

            'overdue' => UserTask::Filters($data, $columns)
                ->tap(fn($q) => $this->applyTaskStatusFilter($q, 'overdue'))
                ->count(),
        ];
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
        // dd($data);
        $request->validate(
            [
                'staff_manager_id' => 'required',
                'entity_type' => 'required|in:lead,client',
                // 'work_manager_id' => 'required|exists:work_managers,id',
            ],
            [
                'staff_manager_id.required' => 'Please select a staff member.',
                'entity_type.required' => 'Please select a entity type.',
                // 'work_manager_id.required' => 'Please select a work.',
            ]
        );

        try {

            $task = new UserTask();
            $task->fill($data);
            $task->created_by = auth()->id();
            $task->created_at = now();
            $task->save();


            return response()->json(['success' => true, 'message' => 'Task created successfully'], 200);
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
        // $staffs = User::whereDoesntHave('roles', function ($q) {
        //     $q->where('name', 'Super Admin');
        // })->get();
        $staffs = User::get();
        $clients = Client::where('status', '1')->get();
        $statuses = StatusManager::where('status', '1')->get();
        $priorities = PriorityManager::where('status', '1')->get();

        $leads = Lead::activeUnconverted()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();


        if ($id != 'new') {
            $taskData = UserTask::find($id);

            return view('admin.user_task.user-task-form', [
                'taskData' => $taskData,
                'clients' => $clients,
                'staffs' => $staffs,
                'statuses' => $statuses,
                'priorities' => $priorities,
                'leads' => $leads,
            ]);
        } else {
            return view('admin.user_task.user-task-form', compact([
                'clients',
                'staffs',
                'statuses',
                'priorities',
                'leads',
            ]));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        // dd($data);
        $request->validate(
            [
                'staff_manager_id' => 'required',
                'entity_type' => 'required|in:lead,client',
                // 'work_manager_id' => 'required',
            ],
            [
                'staff_manager_id.required' => 'Please select a staff member.',
                'entity_type.required' => 'Please select a entity type.',
                // 'work_manager_id.required' => 'Please select a work.',
            ]
        );

        try {
            $task = UserTask::find($id);
            $task->fill($data);
            $task->updated_at = now();
            $task->save();

            return response()->json(['success' => true, 'message' => 'Task updated successfully'], 200);
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
            $task = UserTask::findOrFail($id);
            $task->delete();
            return response()->json(['success' => true, 'message' => 'Task deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function activities(UserTask $task)
    {
        $task->load(['activities.user']);

        $html = view('admin.user_task.activity-modal', compact('task'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ], 200);
    }
}
