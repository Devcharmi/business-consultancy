<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use App\Models\LeadFollowUp;
use App\Models\StatusManager;
use App\Models\Task;
use App\Models\UserTask;
use App\Services\StatusUpdateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }
        // $userExpertiseIds = $user->expertiseManagers()->pluck('expertise_managers.id');

        $dateRange = $request->input('date_range');

        $fromDate = null;
        $toDate   = null;

        if ($dateRange) {
            [$from, $to] = explode(' - ', $dateRange);
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate   = Carbon::parse($to)->endOfDay();
        }

        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        $expertises = ExpertiseManager::activeExpertise()
            ->accessibleBy($user)
            ->get(['id', 'name', 'color_name']);

        $statuses = StatusManager::whereIn('name', ['Done'])
            ->pluck('id', 'name');

        $DONE_STATUS_ID = $statuses['Done'] ?? null;

        $expertiseTasksQuery = Task::accessibleBy($user)
            ->select(
                'expertise_manager_id',
                DB::raw('COUNT(*) as total_tasks'),
                DB::raw("SUM(CASE WHEN status_manager_id = $DONE_STATUS_ID THEN 1 ELSE 0 END) as done_tasks")
            );

        if ($fromDate && $toDate) {
            $expertiseTasksQuery->whereBetween('task_start_date', [$fromDate, $toDate]);
        }

        $expertiseTasks = $expertiseTasksQuery
            ->groupBy('expertise_manager_id')
            ->get();

        $expertiseTaskCounts = $expertiseTasks->keyBy('expertise_manager_id');

        // $consultingQuery = Consulting::with(['expertise_manager', 'client_objective.client'])
        //     ->whereMonth('consulting_date', $selectedMonth)
        //     ->whereYear('consulting_date', $selectedYear)
        //     ->accessibleBy($user);
        $consultingQuery = Consulting::with(['expertise_manager', 'client_objective.client'])
            ->accessibleBy($user);

        if ($fromDate && $toDate) {
            $consultingQuery->whereBetween('consulting_date', [$fromDate, $toDate]);
        } else {
            // fallback month/year (optional)
            $consultingQuery
                ->whereMonth('consulting_date', $selectedMonth)
                ->whereYear('consulting_date', $selectedYear);
        }

        $consultings = $consultingQuery
            ->orderBy('consulting_date')
            ->get();

        $consultingsByDate = [];
        foreach ($consultings as $consulting) {
            $date = Carbon::parse($consulting->consulting_date)->format('Y-m-d');
            if (!isset($consultingsByDate[$date])) {
                $consultingsByDate[$date] = [];
            }
            $consultingsByDate[$date][] = $consulting;
        }

        $currentDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        $canCreateConsulting = $user->can('consulting.create');
        $todayData = $this->getTodayDashboardData($user, $fromDate, $toDate);

        return view('admin.dashboard', array_merge(
            compact(
                'expertises',
                'consultingsByDate',
                'selectedMonth',
                'selectedYear',
                'monthName',
                'prevMonth',
                'nextMonth',
                'canCreateConsulting',
                'expertiseTaskCounts'
            ),
            $todayData
        ));
    }

    private function getTodayDashboardData($user, $fromDate = null, $toDate = null)
    {
        $today = today();
        $isRange = $fromDate && $toDate;

        /* ================= FOLLOW UPS ================= */
        $followUps = LeadFollowUp::query();

        if ($isRange) {
            $followUps->whereBetween('next_follow_up_at', [$fromDate, $toDate]);
        } else {
            $followUps->whereDate('next_follow_up_at', $today);
        }

        /* ================= TASK BASE QUERY ================= */
        $isAdmin = $user->hasRole(['Super Admin', 'Admin']);

        $taskBase = UserTask::with('status_manager')
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('created_by', $user->id)
                        ->orWhere('staff_manager_id', $user->id);
                });
            });

        /* ================= TODAY TASKS ================= */
        $todayTasks = clone $taskBase;

        if ($isRange) {
            // 🔥 Based on task_start_date for selected range
            $todayTasks->whereBetween('task_start_date', [$fromDate, $toDate]);
        } else {
            // 🔥 Default → task_start_date = today
            $todayTasks->whereDate('task_start_date', $today);
        }
        // Exclude Pending status
        $todayTasks->whereHas('status_manager', fn($q) => $q->where('name', '!=', 'Pending'));


        /* ================= PENDING TASKS ================= */
        $pendingTasks = clone $taskBase;

        if ($isRange) {
            $pendingTasks
                ->whereDate('task_start_date', '<=', $toDate)
                ->whereDate('task_due_date', '>=', $fromDate);
        } else {
            $pendingTasks
                ->whereDate('task_start_date', '<=', $today)
                ->whereDate('task_due_date', '>=', $today);
        }

        // 🔥 Exclude Done
        $pendingTasks->whereHas('status_manager', function ($q) {
            $q->where('name', 'Pending');
        });


        /* ================= OVERDUE TASKS ================= */
        $overdueTasks = clone $taskBase;

        if ($isRange) {
            $overdueTasks->whereDate('task_due_date', '<=', $toDate)
                ->whereDate('task_due_date', '>=', $fromDate);
        } else {
            $overdueTasks->whereDate('task_due_date', '<', $today);
        }

        $overdueTasks->whereHas('status_manager', fn($q) => $q->where('name', '!=', 'Done'));
        // // 🔥 Always before today (NOT based on range)
        // $overdueTasks
        //     ->whereDate('task_due_date', '<', $today)
        //     ->whereHas('status_manager', function ($q) {
        //         $q->where('name', '!=', 'Done');
        //     });

        // /* ================= DONE TASKS ================= */
        // $doneTasks = clone $taskBase;
        // if ($isRange) {
        //     // If range selected, show tasks done in range based on completed_at or updated_at
        //     $doneTasks->whereHas('status_manager', fn($q) => $q->where('name', 'Done'))
        //         ->whereBetween('updated_at', [$fromDate, $toDate]);
        // } else {
        //     $doneTasks->whereHas('status_manager', fn($q) => $q->where('name', 'Done'))
        //         ->whereDate('updated_at', $today);
        // }

        return [
            'todayFollowUps' => $followUps->latest()->take(5)->get(),
            'todayTasks'     => $todayTasks->latest()->take(5)->get(),
            'pendingTasks'   => $pendingTasks->latest()->take(5)->get(),
            'overdueTasks'   => $overdueTasks->latest()->take(5)->get(),
            // 'doneTasks'      => $doneTasks->latest()->take(5)->get(), // new
        ];
    }

    // private function getTodayDashboardData($user, $fromDate = null, $toDate = null)
    // {
    //     $today = today();

    //     /* ================= FOLLOW UPS ================= */
    //     $followUps = LeadFollowUp::query();

    //     if ($fromDate && $toDate) {
    //         $followUps->whereBetween('next_follow_up_at', [$fromDate, $toDate]);
    //     } else {
    //         $followUps->whereDate('next_follow_up_at', $today);
    //     }

    //     /* ================= TASK BASE QUERY ================= */
    //     // $taskBase = UserTask::with('status_manager');
    //     $isAdmin = $user->hasRole(['Super Admin', 'Admin']);
    //     $taskBase = UserTask::with('status_manager')
    //         ->when(!$isAdmin, function ($q) use ($user) {
    //             $q->where(function ($sub) use ($user) {
    //                 $sub->where('created_by', $user->id)
    //                     ->orWhere('staff_manager_id', $user->id);
    //             });
    //         });


    //     /* ================= TODAY / RANGE TASKS ================= */
    //     $todayTasks = clone $taskBase;

    //     if ($fromDate && $toDate) {
    //         $todayTasks->whereBetween('task_due_date', [$fromDate, $toDate]);
    //     } else {
    //         $todayTasks->whereDate('task_due_date', $today);
    //     }

    //     /* ================= PENDING TASKS ================= */
    //     $pendingTasks = clone $taskBase;

    //     if ($fromDate && $toDate) {
    //         $pendingTasks
    //             ->whereDate('task_start_date', '<=', $toDate)
    //             ->whereDate('task_due_date', '>=', $fromDate);
    //     } else {
    //         $pendingTasks
    //             ->whereDate('task_start_date', '<=', $today)
    //             ->whereDate('task_due_date', '>=', $today);
    //     }

    //     $pendingTasks->whereHas(
    //         'status_manager',
    //         fn($q) =>
    //         $q->where('name', '!=', 'Done')
    //     );

    //     /* ================= OVERDUE TASKS ================= */
    //     $overdueTasks = clone $taskBase;

    //     if ($fromDate && $toDate) {
    //         $overdueTasks
    //             ->whereDate('task_due_date', '<', $toDate)
    //             ->whereHas(
    //                 'status_manager',
    //                 fn($q) =>
    //                 $q->where('name', '!=', 'Done')
    //             );
    //     } else {
    //         $overdueTasks
    //             ->whereDate('task_due_date', '<', $today)
    //             ->whereHas(
    //                 'status_manager',
    //                 fn($q) =>
    //                 $q->where('name', '!=', 'Done')
    //             );
    //     }

    //     return [
    //         'todayFollowUps' => $followUps->latest()->take(3)->get(),
    //         'todayTasks'     => $todayTasks->latest()->take(3)->get(),
    //         'pendingTasks'   => $pendingTasks->latest()->take(3)->get(),
    //         'overdueTasks'   => $overdueTasks->latest()->take(3)->get(),
    //     ];
    // }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'type' => 'required|in:followup,task',
            'id'   => 'required|integer',
            'status' => 'required|string',
        ]);

        StatusUpdateService::update(
            $request->type,
            $request->id,
            $request->status
        );

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
        ]);
    }

    public function dayConsultings(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = auth()->user();
        $date = $request->date;

        $consultings = Consulting::with([
            'expertise_manager',
            'client_objective.client',
            'client_objective.objective_manager',
        ])
            ->whereDate('consulting_date', $date)
            ->accessibleBy($user)
            ->latest('consulting_date')
            ->get();

        return view('admin.day-consulting-modal', compact(
            'consultings',
            'date'
        ))->render();
    }
}
