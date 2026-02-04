<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function clientReport()
    {
        return view('admin.reports.client');
    }
}
