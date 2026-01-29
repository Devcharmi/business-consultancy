<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Consulting;
use App\Models\ExpertiseManager;
use App\Models\StatusManager;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // public function dashboard()
    // {
    //     $user = auth()->user();
    //     // Log::info(auth()->user());
    //     // Optional: ensure user is authenticated
    //     if (!$user) {
    //         return redirect()->route('login');
    //     }

    //     // // Check for User role
    //     // if ($user->hasRole('User')) {
    //     //     // Log::info($user->roles->first()?->name);
    //     //     return view('user.dashboard');
    //     // }

    //     // Default dashboard for super admin n others roles
    //     return view('admin.dashboard');
    // }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }
        $userExpertiseIds = $user->expertiseManagers()->pluck('expertise_managers.id');

        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');

        $expertises = ExpertiseManager::activeExpertise()
            ->get(['id', 'name', 'color_name']);

        $statuses = StatusManager::whereIn('name', ['Done'])
            ->pluck('id', 'name');

        $DONE_STATUS_ID = $statuses['Done'] ?? null;

        $expertiseTasks = Task::select(
            'expertise_manager_id',
            DB::raw('COUNT(*) as total_tasks'),
            DB::raw("SUM(CASE WHEN status_manager_id = $DONE_STATUS_ID THEN 1 ELSE 0 END) as done_tasks")
        )
            ->groupBy('expertise_manager_id')
            ->get();

        $expertiseTaskCounts = $expertiseTasks->keyBy('expertise_manager_id');

        // $consultings = Consulting::with(['expertise_manager', 'client_objective.client'])
        //     // ->whereIn('expertise_manager_id', $userExpertiseIds)
        //     ->whereMonth('consulting_datetime', $selectedMonth)
        //     ->whereYear('consulting_datetime', $selectedYear)
        //     ->orderBy('consulting_datetime')
        //     ->get();
        $consultingQuery = Consulting::with(['expertise_manager', 'client_objective.client'])
            ->whereMonth('consulting_datetime', $selectedMonth)
            ->whereYear('consulting_datetime', $selectedYear);

        if (!$user->hasRole('Super Admin')) {
            $consultingQuery->whereIn(
                'expertise_manager_id',
                $user->expertiseManagers()->pluck('expertise_managers.id')
            );
        }

        $consultings = $consultingQuery
            ->orderBy('consulting_datetime')
            ->get();

        $consultingsByDate = [];
        foreach ($consultings as $consulting) {
            $date = Carbon::parse($consulting->consulting_datetime)->format('Y-m-d');
            if (!isset($consultingsByDate[$date])) {
                $consultingsByDate[$date] = [];
            }
            $consultingsByDate[$date][] = $consulting;
        }

        $currentDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        $canCreateConsulting = $user->can('consulting.create');

        return view('admin.dashboard', compact(
            'expertises',
            'consultingsByDate',
            'selectedMonth',
            'selectedYear',
            'monthName',
            'prevMonth',
            'nextMonth',
            'canCreateConsulting',
            'expertiseTaskCounts'
        ));
    }
}
