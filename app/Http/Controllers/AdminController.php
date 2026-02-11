<?php

namespace App\Http\Controllers;
// Import Client model at the top if not exists
use App\Models\Client;
use App\Models\DentalOffice;
use App\Models\Sale;
use App\Models\User;
use App\Models\Task;
use App\Models\Iteration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        // Initialize variables
        $total_sale = 0;
        $total_sale_count = 0;
        $dental_offices = collect();
        $contacted_dental_offices = '';
        $active_won = '';
        $active_tasks = [];
        $past_tasks = [];

        // Auto-Pilot Stats Defaults
        $pending_tasks = 0;
        $completed_tasks = 0;
        $total_completed_tasks = 0;
        $current_iteration = 1;

        // --- 1. COUNTRY MANAGER LOGIC ---
        if ($user->hasRole('CountryManager')) {
            $total_sale = Sale::whereYear('created_at', $currentYear)->sum('sale_value');
            $total_sale_count = Sale::whereYear('created_at', $currentYear)->count('id');
            $dental_offices = DentalOffice::latest()->take(10)->get();

            // Task Stats (Global)
            $pending_tasks = Task::where('status', 'pending')->count();

            $completed_tasks = Task::whereIn('status', ['completed', 'converted'])
                ->whereDate('updated_at', Carbon::today())
                ->count();

            $total_completed_tasks = Task::whereIn('status', ['completed', 'converted'])->count();

            // Iteration Calculation
            $latestIteration = Iteration::latest('id')->first();
            $current_iteration = $latestIteration ? $latestIteration->id : 0;
        }
        // --- 2. REGIONAL MANAGER LOGIC ---
        elseif ($user->hasRole('RegionalManager')) {
            // Identify Team (Area Managers & Sales Reps in this Region)
            $teamIds = User::where('regional_manager_id', $user->id)->pluck('id')->toArray();
            $teamIds[] = $user->id;

            // Sales Stats
            $total_sale = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->sum('sale_value');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->count('id');

            // Offices in my regions
            $dental_offices = DentalOffice::whereIn('region_id', $user->regions->pluck('id'))
                ->latest()
                ->take(10)
                ->get();

            // Task Stats for Region
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
            // Identify Team
            $teamIds = User::where('state_manager_id', $user->id)->pluck('id')->toArray();
            $teamIds[] = $user->id;

            // Sales Stats
            $total_sale = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->sum('sale_value');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)
                ->whereIn('sales_rep_id', $teamIds)
                ->count('id');

            // Offices in my states
            $dental_offices = DentalOffice::whereIn('state_id', $user->states->pluck('id'))
                ->latest()
                ->take(10)
                ->get();

            // Task Stats for Area
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

            // Personal Tasks
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

        // Get Active Iteration ID
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

    // --- CHART DATA FUNCTIONS (Kept for Dashboard) ---

    // 1. DYNAMIC SALES & REVENUE (Chart)
    // Now tracks Client Subscriptions over time
    public function get_monthly_sales(Request $request)
    {
        $range = $request->input('range', 'year');
        $query = Client::where('status', 'Active');

        // Hierarchy Logic
        if (Auth::user()->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('SalesRepresentative')) {
            $query->where('sales_rep_id', Auth::user()->id);
        }

        if ($range == 'week') {
            $query
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%a") as label'),
                    DB::raw('SUM(subscription_amount) as total'),
                ); // CHANGED column
            $groupBy = 'label';
        } elseif ($range == 'month') {
            $query
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->select(
                    DB::raw(
                        'WEEK(created_at) - WEEK(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY)) + 1 as label',
                    ),
                    DB::raw('SUM(subscription_amount) as total'),
                );
            $groupBy = 'label';
        } else {
            $query
                ->whereYear('created_at', Carbon::now()->year)
                ->select(DB::raw('MONTHNAME(created_at) as label'), DB::raw('SUM(subscription_amount) as total'));
            $groupBy = 'label';
        }

        $sales = $query->groupBy(DB::raw($groupBy))->get();

        return response()->json(['labels' => $sales->pluck('label'), 'series' => $sales->pluck('total')]);
    }

    // 2. TOP SALES REPS (Leaderboard)
    // Now ranks by Total Subscription Value brought in
    public function get_top_sales(Request $request)
    {
        $range = $request->input('range', 'year');

        // CHANGED: From Sale to Client, Summing subscription_amount
        $query = Client::where('status', 'Active')
            ->select('sales_rep_id', DB::raw('SUM(subscription_amount) as total_sales'))
            ->groupBy('sales_rep_id')
            ->orderByRaw('SUM(subscription_amount) DESC')
            ->take(5);

        if ($range == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month);
        }
        if ($range == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
        if ($range == 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        // Hierarchy
        if (Auth::user()->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        }

        $salesData = $query->get();

        $names = [];
        $values = [];

        foreach ($salesData as $sale) {
            $user = User::find($sale->sales_rep_id);
            $names[] = $user ? $user->name : 'Unknown';
            $values[] = $sale->total_sales;
        }

        return response()->json(['labels' => $names, 'series' => $values]);
    }

    // 3. SUBSCRIPTIONS / ENGAGEMENT
    // Now counts Clients based on 'subscription_amount' tier (Example logic)
    public function get_subscriptions_sale(Request $request)
    {
        $range = $request->input('range', 'year');
        $query = Client::where('status', 'Active');

        if (Auth::user()->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('SalesRepresentative')) {
            $query->where('sales_rep_id', Auth::user()->id);
        }

        // Logic: Assuming > $500 is Premium, else Standard (Adjust as needed)
        if ($range == 'week') {
            $query
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%a") as label'),
                    DB::raw('SUM(CASE WHEN subscription_amount < 500 THEN 1 ELSE 0 END) as standard'),
                    DB::raw('SUM(CASE WHEN subscription_amount >= 500 THEN 1 ELSE 0 END) as premium'),
                );
            $groupBy = 'label';
        } else {
            $query
                ->whereYear('created_at', Carbon::now()->year)
                ->select(
                    DB::raw('MONTHNAME(created_at) as label'),
                    DB::raw('SUM(CASE WHEN subscription_amount < 500 THEN 1 ELSE 0 END) as standard'),
                    DB::raw('SUM(CASE WHEN subscription_amount >= 500 THEN 1 ELSE 0 END) as premium'),
                );
            $groupBy = 'label';
        }

        $data = $query->groupBy(DB::raw($groupBy))->get();

        return response()->json([
            'labels' => $data->pluck('label'),
            'standard' => $data->pluck('standard'),
            'premium' => $data->pluck('premium'),
        ]);
    }

    // 4. TOTAL SALES HEADER (Big Number)
    // Sums up all subscription amounts
    public function get_total_sale(Request $request)
    {
        $range = $request->input('range', 'year');
        // CHANGED: Filter for Active clients only
        $query = Client::where('status', 'Active');

        if ($range == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month);
        }
        if ($range == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
        if ($range == 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        if (Auth::user()->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('SalesRepresentative')) {
            $query->where('sales_rep_id', Auth::user()->id);
        }

        // Sum subscription_amount instead of sale_value
        return response()->json(['total' => $query->sum('subscription_amount')]);
    }

    public function get_weekly_sales()
    {
        $currentDate = Carbon::now();
        $currentMonthStart = $currentDate->copy()->startOfMonth();
        $previousMonthStart = $currentMonthStart->copy()->subMonth()->startOfMonth();
        $weeklySales = [];

        // Determine Team IDs based on Role (Standard Logic)
        $teamIds = null;
        if (Auth::user()->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
        } elseif (Auth::user()->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', Auth::user()->id)->pluck('id');
        } elseif (Auth::user()->hasRole('SalesRepresentative')) {
            $teamIds = [Auth::user()->id];
        }

        // Loop through 4 weeks
        for ($i = 0; $i < 4; $i++) {
            $cStart = $currentMonthStart->copy()->addWeeks($i)->startOfWeek();
            $cEnd = $cStart->copy()->endOfWeek();
            $pStart = $previousMonthStart->copy()->addWeeks($i)->startOfWeek();
            $pEnd = $pStart->copy()->endOfWeek();

            // QUERY 1: CURRENT MONTH (This Week)
            // CHANGED: Use Client, Filter Active, Sum subscription_amount
            $cQuery = Client::where('status', 'Active')->whereBetween('created_at', [$cStart, $cEnd]);

            // QUERY 2: PREVIOUS MONTH (Same Week Last Month)
            $pQuery = Client::where('status', 'Active')->whereBetween('created_at', [$pStart, $pEnd]);

            // Apply Hierarchy Filters
            if ($teamIds) {
                $cQuery->whereIn('sales_rep_id', $teamIds);
                $pQuery->whereIn('sales_rep_id', $teamIds);
            }

            // Build the data structure for ApexCharts
            $weeklySales[] = [
                'current_week' => [
                    'start_date' => $cStart->format('Y-m-d'),
                    'end_date' => $cEnd->format('Y-m-d'),
                    'total_sales' => $cQuery->sum('subscription_amount'), // <--- Fixed Sum
                ],
                'previous_week' => [
                    'start_date' => $pStart->format('Y-m-d'),
                    'end_date' => $pEnd->format('Y-m-d'),
                    'total_sales' => $pQuery->sum('subscription_amount'), // <--- Fixed Sum
                ],
            ];
        }

        return response()->json($weeklySales);
    }

    public function get_won_sales()
    {
        $total = DentalOffice::where('sales_rep_id', Auth::user()->id)->count();
        $won = DentalOffice::where('sales_rep_id', Auth::user()->id)
            ->where('purchase_product', 'Yes')
            ->count();
        return response()->json($total > 0 ? intval(($won / $total) * 100) : 0);
    }

    public function get_reschedule_sales()
    {
        $total = DentalOffice::where('sales_rep_id', Auth::user()->id)->count();
        $reschedule = DentalOffice::where('sales_rep_id', Auth::user()->id)
            ->where('purchase_product', null)
            ->whereNotNull('follow_up_date')
            ->whereNotNull('contact_date')
            ->count();
        return response()->json($total > 0 ? intval(($reschedule / $total) * 100) : 0);
    }

    public function get_schedule_sales()
    {
        $total = DentalOffice::where('sales_rep_id', Auth::user()->id)->count();
        $schedule = DentalOffice::where('sales_rep_id', Auth::user()->id)
            ->where('purchase_product', null)
            ->whereNull('follow_up_date')
            ->whereNotNull('contact_date')
            ->count();
        return response()->json($total > 0 ? intval(($schedule / $total) * 100) : 0);
    }

    public function get_response()
    {
        $hotCount = 0;
        $warmCount = 0;
        $coldCount = 0;
        $query = DentalOffice::query();

        if (Auth::user()->hasRole('RegionalManager')) {
            $teamIds = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('AreaManager')) {
            $teamIds = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $query->whereIn('sales_rep_id', $teamIds);
        } elseif (Auth::user()->hasRole('SalesRepresentative')) {
            $query->where('sales_rep_id', Auth::user()->id);
        }

        // Clone query for each status to avoid resetting
        $hotCount = (clone $query)->where('receptive', 'HOT')->count();
        $warmCount = (clone $query)->where('receptive', 'WARM')->count();
        $coldCount = (clone $query)->where('receptive', 'COLD')->count();

        $total = $hotCount + $warmCount + $coldCount;
        $series =
            $total > 0
                ? [($hotCount / $total) * 100, ($warmCount / $total) * 100, ($coldCount / $total) * 100]
                : [0, 0, 0];

        return response()->json([
            'labels' => ['HOT', 'WARM', 'COLD'],
            'series' => $series,
        ]);
    }
}
