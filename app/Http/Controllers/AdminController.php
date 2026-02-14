<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\DentalOffice;
use App\Models\Sale;
use App\Models\User;
use App\Models\Task;
use App\Models\Iteration;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // --- MAIN DASHBOARD VIEW ---
    public function index()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        // Initialize variables
        $total_sale = 0;
        $total_sale_count = 0;
        $dental_offices = collect();
        $contacted_dental_offices = [];
        $active_won = 0;
        $active_tasks = [];
        $past_tasks = [];
        $pending_tasks = 0;
        $completed_tasks = 0;
        $total_completed_tasks = 0;
        $current_iteration = 1;

        // --- 1. COUNTRY MANAGER LOGIC ---
        if ($user->hasRole('CountryManager')) {
            $total_sale = Sale::whereYear('created_at', $currentYear)->sum('sale_value');
            $total_sale_count = Sale::whereYear('created_at', $currentYear)->count('id');
            $dental_offices = DentalOffice::latest()->take(10)->get();
            $pending_tasks = Task::where('status', 'pending')->count();
            $completed_tasks = Task::whereIn('status', ['completed', 'converted'])
                ->whereDate('updated_at', Carbon::today())
                ->count();
            $total_completed_tasks = Task::whereIn('status', ['completed', 'converted'])->count();
            $latestIteration = Iteration::latest('id')->first();
            $current_iteration = $latestIteration ? $latestIteration->id : 0;
        }
        // --- 2. REGIONAL MANAGER LOGIC ---
        elseif ($user->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', $user->id)->pluck('id')->toArray();
            $teamIds[] = $user->id;
            $total_sale = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->sum('sale_value');
            $total_sale_count = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->count('id');
            $dental_offices = DentalOffice::whereIn('region_id', $user->regions->pluck('id'))
                ->latest()
                ->take(10)
                ->get();
            $pending_tasks = Task::whereIn('user_id', $teamIds)->where('status', 'pending')->count();
            $completed_tasks = Task::whereIn('user_id', $teamIds)
                ->whereIn('status', ['completed', 'converted'])
                ->whereDate('updated_at', Carbon::today())
                ->count();
            $total_completed_tasks = Task::whereIn('user_id', $teamIds)
                ->whereIn('status', ['completed', 'converted'])
                ->count();
            $total_region_tasks = Task::whereIn('user_id', $teamIds)->count();
            $current_iteration = max(ceil($total_region_tasks / 50), 1);
        }
        // --- 3. AREA MANAGER LOGIC ---
        elseif ($user->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', $user->id)->pluck('id')->toArray();
            $teamIds[] = $user->id;
            $total_sale = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->sum('sale_value');
            $total_sale_count = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->count('id');
            $dental_offices = DentalOffice::whereIn('state_id', $user->states->pluck('id'))
                ->latest()
                ->take(10)
                ->get();
            $pending_tasks = Task::whereIn('user_id', $teamIds)->where('status', 'pending')->count();
            $completed_tasks = Task::whereIn('user_id', $teamIds)
                ->whereIn('status', ['completed', 'converted'])
                ->whereDate('updated_at', Carbon::today())
                ->count();
            $total_completed_tasks = Task::whereIn('user_id', $teamIds)
                ->whereIn('status', ['completed', 'converted'])
                ->count();
            $total_area_tasks = Task::whereIn('user_id', $teamIds)->count();
            $current_iteration = max(ceil($total_area_tasks / 50), 1);
        }
        // --- 4. SALES REP LOGIC ---
        elseif ($user->hasRole('SalesRepresentative')) {
            $total_sale = Sale::where('sales_rep_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->sum('sale_value');
            $total_sale_count = Sale::where('sales_rep_id', $user->id)
                ->whereYear('created_at', $currentYear)
                ->count('id');
            $dental_offices = DentalOffice::where('sales_rep_id', $user->id)->latest()->take(10)->get();
            $week_start = now()->startOfWeek()->format('Y-m-d');
            $week_end = now()->endOfWeek()->format('Y-m-d');
            $contacted_dental_offices = DentalOffice::where('sales_rep_id', $user->id)
                ->whereBetween('contact_date', [$week_start, $week_end])
                ->get();
            $active_won = DentalOffice::where('sales_rep_id', $user->id)->where('purchase_product', 'Yes')->count('id');
            $active_tasks = Task::with('dentalOffice')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->orderBy('id', 'desc')
                ->get();
            $past_tasks = Task::with('dentalOffice')
                ->where('user_id', $user->id)
                ->whereIn('status', ['completed', 'converted'])
                ->orderBy('updated_at', 'desc')
                ->take(20)
                ->get();
        }

        $activeIteration = Iteration::where('status', 'active')->first();
        $batchId = $activeIteration ? $activeIteration->id : $current_iteration;

        return view(
            'dashboard',
            compact(
                'total_sale',
                'total_sale_count',
                'dental_offices',
                'contacted_dental_offices',
                'active_won',
                'active_tasks',
                'past_tasks',
                'pending_tasks',
                'completed_tasks',
                'total_completed_tasks',
                'current_iteration',
                'batchId',
                'activeIteration',
            ),
        );
    }

    // --- HELPER: Base Query with State & Role Filter ---
    private function getBaseQuery($table, $request)
    {
        $user = Auth::user();
        $stateName = $request->input('state');

        $query =
            $table === 'Client'
                ? Client::where('status', 'Active')
                : ($table === 'DentalOffice'
                    ? DentalOffice::query()
                    : Task::query());

        // 1. Role Filters
        $teamIds = [];
        if ($user->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', $user->id)->pluck('id');
        } elseif ($user->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', $user->id)->pluck('id');
        } elseif ($user->hasRole('SalesRepresentative')) {
            $teamIds = [$user->id];
        }

        if (!empty($teamIds)) {
            if ($table === 'Task') {
                $query->whereIn('user_id', $teamIds);
            } else {
                $query->whereIn('sales_rep_id', $teamIds);
            }
        }

        // 2. State Filter
        if ($stateName && $stateName !== 'null') {
            $state = State::where('name', $stateName)->first();
            if ($state) {
                if ($table === 'DentalOffice') {
                    $query->where('state_id', $state->id);
                } else {
                    $query->whereHas('dentalOffice', function ($q) use ($state) {
                        $q->where('state_id', $state->id);
                    });
                }
            }
        }
        return $query;
    }

    // --- 1. GET TOTAL SALE (Amount + Count for Circular Chart) ---
    // public function get_total_sale(Request $request)
    // {
    //     $range = $request->input('range', 'year');
    //     $query = $this->getBaseQuery('Client', $request);

    //     // Apply Time Filters
    //     if ($range == 'month') {
    //         $query->whereMonth('created_at', Carbon::now()->month);
    //     } elseif ($range == 'week') {
    //         $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    //     } elseif ($range == 'today') {
    //         $query->whereDate('created_at', Carbon::today());
    //     } else {
    //         $query->whereYear('created_at', Carbon::now()->year);
    //     }

    //     return response()->json([
    //         'total_amount' => number_format($query->sum('subscription_amount'), 2),
    //         'total_count' => $query->count(),
    //     ]);
    // }

    public function get_total_sale(Request $request)
    {
        $range = $request->input('range', 'year');
        // The getBaseQuery helper already handles the 'state' input automatically
        $query = $this->getBaseQuery('Client', $request);

        if ($range == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month);
        } elseif ($range == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } else {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        return response()->json([
            'total_amount' => number_format($query->sum('subscription_amount'), 2),
            'total_count' => $query->count(),
        ]);
    }
    // --- 2. SALES RECORD (Spline) ---
    public function get_monthly_sales(Request $request)
    {
        $range = $request->input('range', 'year');
        $query = $this->getBaseQuery('Client', $request);
        $labels = [];
        $data = [];

        if ($range == 'today') {
            for ($i = 0; $i < 24; $i += 4) {
                $start = Carbon::today()->addHours($i);
                $end = Carbon::today()
                    ->addHours($i + 3)
                    ->endOfHour();
                $labels[] = $start->format('g A');
                $data[] = (clone $query)->whereBetween('created_at', [$start, $end])->sum('subscription_amount');
            }
        } elseif ($range == 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('D');
                $data[] = (clone $query)->whereDate('created_at', $date)->sum('subscription_amount');
            }
        } elseif ($range == 'month') {
            for ($i = 0; $i < 4; $i++) {
                $start = Carbon::now()->startOfMonth()->addWeeks($i);
                $end = $start->copy()->endOfWeek();
                $labels[] = 'W' . ($i + 1);
                $data[] = (clone $query)->whereBetween('created_at', [$start, $end])->sum('subscription_amount');
            }
        } elseif ($range == 'quarter') {
            for ($i = 2; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M');
                $data[] = (clone $query)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('subscription_amount');
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->format('M');
                $data[] = (clone $query)
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('subscription_amount');
            }
        }
        return response()->json(['labels' => $labels, 'series' => [['name' => 'Sales', 'data' => $data]]]);
    }

    // --- 3. REVENUE CHART ---
    public function get_weekly_sales(Request $request)
    {
        $range = $request->input('range', 'weekly');
        $query = $this->getBaseQuery('Client', $request);
        $labels = [];
        $currentData = [];

        if ($range == 'weekly') {
            for ($i = 3; $i >= 0; $i--) {
                $start = Carbon::now()->subWeeks($i)->startOfWeek();
                $end = Carbon::now()->subWeeks($i)->endOfWeek();
                $labels[] = $i === 0 ? 'This Week' : $start->format('M d');
                $currentData[] = (clone $query)->whereBetween('created_at', [$start, $end])->sum('subscription_amount');
            }
        } else {
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M Y');
                $currentData[] = (clone $query)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('subscription_amount');
            }
        }
        return response()->json(['labels' => $labels, 'series' => [['name' => 'Revenue', 'data' => $currentData]]]);
    }

    // --- 4. RESPONSE PIE CHART ---
    public function get_response(Request $request)
    {
        $query = $this->getBaseQuery('DentalOffice', $request);
        return response()->json([
            'labels' => ['HOT', 'WARM', 'COLD'],
            'series' => [
                (clone $query)->where('receptive', 'HOT')->count(),
                (clone $query)->where('receptive', 'WARM')->count(),
                (clone $query)->where('receptive', 'COLD')->count(),
            ],
        ]);
    }

    // --- 5. TOP SALES ---
    public function get_top_sales(Request $request)
    {
        $range = $request->input('range', 'year');
        $query = $this->getBaseQuery('Client', $request);

        if ($range == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month);
        }
        if ($range == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }

        $salesData = $query
            ->select('sales_rep_id', DB::raw('SUM(subscription_amount) as total'))
            ->groupBy('sales_rep_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $names = [];
        $values = [];
        foreach ($salesData as $sale) {
            $user = User::find($sale->sales_rep_id);
            $names[] = $user ? $user->name : 'Unknown';
            $values[] = $sale->total;
        }
        return response()->json(['labels' => $names, 'series' => [['name' => 'Sales', 'data' => $values]]]);
    }

    // --- 6. SUBSCRIPTION ---
    public function get_subscriptions_sale(Request $request)
    {
        $query = $this->getBaseQuery('Client', $request);
        $labels = [];
        $std = [];
        $prm = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $labels[] = $m->format('M');
            $monthQ = (clone $query)->whereMonth('created_at', $m->month)->whereYear('created_at', $m->year);
            $std[] = (clone $monthQ)->where('subscription_amount', '<', 500)->count();
            $prm[] = (clone $monthQ)->where('subscription_amount', '>=', 500)->count();
        }
        return response()->json(['labels' => $labels, 'standard' => $std, 'premium' => $prm]);
    }

    // --- 7. CAPTURING STATS ---
    public function get_capturing_stats(Request $request)
    {
        $range = $request->input('range', 'month');
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $revenueGoal = 10000;
        $activityGoal = 200;

        if ($range == 'today') {
            $start = Carbon::today();
            $end = Carbon::today()->endOfDay();
            $revenueGoal = 500;
            $activityGoal = 10;
        }
        if ($range == 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $revenueGoal = 2500;
            $activityGoal = 50;
        }
        if ($range == 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $revenueGoal = 120000;
            $activityGoal = 2400;
        }

        $clientQ = $this->getBaseQuery('Client', $request)->whereBetween('created_at', [$start, $end]);
        $taskQ = $this->getBaseQuery('Task', $request)
            ->whereIn('status', ['completed', 'converted'])
            ->whereBetween('updated_at', [$start, $end]);
        $dentalQ = $this->getBaseQuery('DentalOffice', $request)->whereBetween('contact_date', [$start, $end]);

        $revActual = $clientQ->sum('subscription_amount');
        $revPct = $revenueGoal > 0 ? round(($revActual / $revenueGoal) * 100) : 0;
        $convCount = $clientQ->count();
        $contCount = $dentalQ->count() ?: $taskQ->count();
        $convPct = $contCount > 0 ? round(($convCount / $contCount) * 100) : 0;
        $actCount = $taskQ->count();
        $actPct = $activityGoal > 0 ? round(($actCount / $activityGoal) * 100) : 0;

        return response()->json([
            'revenue' => ['percent' => $revPct, 'label' => '$' . number_format($revActual)],
            'conversion' => ['percent' => $convPct, 'label' => $convCount . ' Won'],
            'activity' => ['percent' => $actPct, 'label' => $actCount . ' Calls'],
        ]);
    }

    // --- 8. DASHBOARD STATIC STATS (Updates Big Numbers) ---
    public function get_dashboard_stats(Request $request)
    {
        $clientQ = $this->getBaseQuery('Client', $request);
        $wonQ = $this->getBaseQuery('DentalOffice', $request)->where('purchase_product', 'Yes');

        // Return both Revenue ($) and Sales Count (#)
        return response()->json([
            'total_revenue' => '$' . number_format($clientQ->sum('subscription_amount'), 2),
            'total_sales_count' => $clientQ->count(), // <--- ADDED THIS FOR THE CIRCLE
            'active_won' => $wonQ->count(),
        ]);
    }
}
