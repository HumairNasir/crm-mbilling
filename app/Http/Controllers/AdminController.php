<?php

namespace App\Http\Controllers;

use App\Models\DentalOffice;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {

        $total_sale = '';
        $total_sale_count = '';
        $dental_offices = '';
        $contacted_dental_offices = '';
        $active_won = '';
        $currentYear = Carbon::now()->year;

        if (Auth::user()->roles[0]->name == 'CountryManager'){

            $total_sale = Sale::whereYear('created_at', $currentYear)->sum('sale_value');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)->count('id');

            $dental_offices = DentalOffice::all();

        }elseif (Auth::user()->roles[0]->name == 'RegionalManager'){

            $total_sale = Sale::whereYear('created_at', $currentYear)->whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('regional_manager_id', Auth::user()->id);
            })->sum('sale_value');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)->whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('regional_manager_id', Auth::user()->id);
            })->count('id');

            $dental_offices = DentalOffice::all();

        }elseif(Auth::user()->roles[0]->name == 'AreaManager'){

            $total_sale = Sale::whereYear('created_at', $currentYear)->whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('state_manager_id', Auth::user()->id);
            })->sum('sale_value');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)->whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('state_manager_id', Auth::user()->id);
            })->count('id');

            $dental_offices = DentalOffice::all();

        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){

            $total_sale = Sale::where('sales_rep_id',Auth::user()->id)->whereYear('created_at', $currentYear)->sum('sale_value');
            $total_sale_count = Sale::where('sales_rep_id',Auth::user()->id)->whereYear('created_at', $currentYear)->count('id');

            $dental_offices = DentalOffice::all();

            $week_start = now()->startOfWeek()->format('Y-m-d');
            $week_end = now()->endOfWeek()->format('Y-m-d');
            $contacted_dental_offices = DentalOffice::where('sales_rep_id', Auth::user()->id)->where('contact_date', '>=', $week_start)->where('contact_date', '<=', $week_end)->get();
            $active_won = DentalOffice::where('sales_rep_id', Auth::user()->id)->where('purchase_product', 'Yes')->count('id');
        }

        return view('dashboard', compact('total_sale','total_sale_count', 'dental_offices', 'contacted_dental_offices','active_won'));
    }

    public function dental_offices()
    {

        return view('dental_offices');

    }



    public function area_manager()
    {


    }

    public function clients()
    {

        return view('client');

    }

    public function sales_rep()
    {

        return view('sales-rep');

    }

    public function get_response()
    {
        $data = '';
        if (Auth::user()->roles[0]->name == 'CountryManager') {

            $hotCount = DentalOffice::where('receptive', 'HOT')->count();
            $warmCount = DentalOffice::where('receptive', 'WARM')->count();
            $coldCount = DentalOffice::where('receptive', 'COLD')->count();

            $totalCount = $hotCount + $warmCount + $coldCount;

            $data = [
                'labels' => ['HOT', 'WARM', 'COLD'],
                'series' => [
                    ($hotCount / $totalCount) * 100,
                    ($warmCount / $totalCount) * 100,
                    ($coldCount / $totalCount) * 100,
                ],
            ];

        }elseif(Auth::user()->roles[0]->name == 'RegionalManager') {

            $areaManager = User::where('regional_manager_id', Auth::user()->id)->pluck('id');

            $hotCount = DentalOffice::where('receptive', 'HOT')->whereIn('sales_rep_id',$areaManager)->count();
            $warmCount = DentalOffice::where('receptive', 'WARM')->whereIn('sales_rep_id',$areaManager)->count();
            $coldCount = DentalOffice::where('receptive', 'COLD')->whereIn('sales_rep_id',$areaManager)->count();

            $totalCount = $hotCount + $warmCount + $coldCount;

            $data = [
                'labels' => ['HOT', 'WARM', 'COLD'],
                'series' => [
                    ($hotCount / $totalCount) * 100,
                    ($warmCount / $totalCount) * 100,
                    ($coldCount / $totalCount) * 100,
                ],
            ];
        }elseif(Auth::user()->roles[0]->name == 'AreaManager') {

            $areaManager = User::where('state_manager_id', Auth::user()->id)->pluck('id');

            $hotCount = DentalOffice::where('receptive', 'HOT')->whereIn('sales_rep_id',$areaManager)->count();
            $warmCount = DentalOffice::where('receptive', 'WARM')->whereIn('sales_rep_id',$areaManager)->count();
            $coldCount = DentalOffice::where('receptive', 'COLD')->whereIn('sales_rep_id',$areaManager)->count();

            $totalCount = $hotCount + $warmCount + $coldCount;

            $data = [
                'labels' => ['HOT', 'WARM', 'COLD'],
                'series' => [
                    ($hotCount / $totalCount) * 100,
                    ($warmCount / $totalCount) * 100,
                    ($coldCount / $totalCount) * 100,
                ],
            ];
        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){

            $hotCount = DentalOffice::where('receptive', 'HOT')->where('sales_rep_id', Auth::user()->id)->count();
            $warmCount = DentalOffice::where('receptive', 'WARM')->where('sales_rep_id', Auth::user()->id)->count();
            $coldCount = DentalOffice::where('receptive', 'COLD')->where('sales_rep_id', Auth::user()->id)->count();

            $totalCount = $hotCount + $warmCount + $coldCount;

            $data = [
                'labels' => ['HOT', 'WARM', 'COLD'],
                'series' => [
                    ($hotCount / $totalCount) * 100,
                    ($warmCount / $totalCount) * 100,
                    ($coldCount / $totalCount) * 100,
                ],
            ];
        }

        return response()->json($data);

    }

    public function get_top_sales()
    {
        $formattedData = '';

        if (Auth::user()->roles[0]->name == 'CountryManager') {

            $salesData = Sale::select('sales_rep_id', DB::raw('SUM(sale_value) as total_sales'))
                ->groupBy('sales_rep_id')
                ->orderByRaw('SUM(sale_value) DESC')
                ->take(10)
                ->get();

            $dentalOfficeIds = $salesData->pluck('sales_rep_id')->toArray();

            $top10DentalOffices = User::whereIn('id', $dentalOfficeIds)
                ->select('id', 'name')
                ->get();

            $formattedData = [];
            foreach ($top10DentalOffices as $dentalOffice) {
                $salesForOffice = $salesData->where('sales_rep_id', $dentalOffice->id)->first();
                if ($salesForOffice) {
                    $formattedData[] = [
                        'dental_office_id' => $dentalOffice->id,
                        'store_name' => $dentalOffice->name,
                        'total_sales' => $salesForOffice->total_sales,
                    ];
                }
            }

        }elseif(Auth::user()->roles[0]->name == 'RegionalManager') {

            $areaManager = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $salesData = Sale::select('sales_rep_id', DB::raw('SUM(sale_value) as total_sales'))
                ->whereIn('sales_rep_id',$areaManager)
                ->groupBy('sales_rep_id')
                ->orderByRaw('SUM(sale_value) DESC')
                ->take(10)
                ->get();

            $dentalOfficeIds = $salesData->pluck('sales_rep_id')->toArray();

            $top10DentalOffices = User::whereIn('id', $dentalOfficeIds)
                ->select('id', 'name')
                ->get();

            $formattedData = [];
            foreach ($top10DentalOffices as $dentalOffice) {
                $salesForOffice = $salesData->where('sales_rep_id', $dentalOffice->id)->first();
                if ($salesForOffice) {
                    $formattedData[] = [
                        'dental_office_id' => $dentalOffice->id,
                        'store_name' => $dentalOffice->name,
                        'total_sales' => $salesForOffice->total_sales,
                    ];
                }
            }
        }elseif(Auth::user()->roles[0]->name == 'AreaManager') {

            $areaManager = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $salesData = Sale::select('sales_rep_id', DB::raw('SUM(sale_value) as total_sales'))
                ->whereIn('sales_rep_id',$areaManager)
                ->groupBy('sales_rep_id')
                ->orderByRaw('SUM(sale_value) DESC')
                ->take(10)
                ->get();

            $dentalOfficeIds = $salesData->pluck('sales_rep_id')->toArray();

            $top10DentalOffices = User::whereIn('id', $dentalOfficeIds)
                ->select('id', 'name')
                ->get();

            $formattedData = [];
            foreach ($top10DentalOffices as $dentalOffice) {
                $salesForOffice = $salesData->where('sales_rep_id', $dentalOffice->id)->first();
                if ($salesForOffice) {
                    $formattedData[] = [
                        'dental_office_id' => $dentalOffice->id,
                        'store_name' => $dentalOffice->name,
                        'total_sales' => $salesForOffice->total_sales,
                    ];
                }
            }
        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){
            $salesData = Sale::select('dental_office_id', DB::raw('SUM(sale_value) as total_sales'))
                ->where('sales_rep_id', Auth::user()->id)
                ->groupBy('dental_office_id')
                ->orderByRaw('SUM(sale_value) DESC')
                ->take(10)
                ->get();

            $dentalOfficeIds = $salesData->pluck('dental_office_id')->toArray();

            $top10DentalOffices = DentalOffice::whereIn('id', $dentalOfficeIds)
                ->select('id', 'name')
                ->get();

            $formattedData = [];
            foreach ($top10DentalOffices as $dentalOffice) {
                $salesForOffice = $salesData->where('dental_office_id', $dentalOffice->id)->first();
                if ($salesForOffice) {
                    $formattedData[] = [
                        'dental_office_id' => $dentalOffice->id,
                        'store_name' => $dentalOffice->name,
                        'total_sales' => $salesForOffice->total_sales,
                    ];
                }
            }
        }

        return response()->json($formattedData);
    }

    public function get_monthly_sales()
    {
        $data = '';

        if (Auth::user()->roles[0]->name == 'CountryManager') {

            $sales = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(sale_value) as total_sales')
            )
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();

            $data = [];

            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];

            foreach ($sales as $sale) {
                $monthNumber = $sale->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'total_sales' => $sale->total_sales,
                ];
            }

        }elseif(Auth::user()->roles[0]->name == 'RegionalManager') {

            $areaManager = User::where('regional_manager_id', Auth::user()->id)->pluck('id');

            $sales = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(sale_value) as total_sales')
            )
                ->whereIn('sales_rep_id', $areaManager)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();

            $data = [];

            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];

            foreach ($sales as $sale) {
                $monthNumber = $sale->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'total_sales' => $sale->total_sales,
                ];
            }
        }elseif(Auth::user()->roles[0]->name == 'AreaManager') {

            $areaManager = User::where('state_manager_id', Auth::user()->id)->pluck('id');

            $sales = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(sale_value) as total_sales')
            )
                ->whereIn('sales_rep_id', $areaManager)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();

            $data = [];

            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];

            foreach ($sales as $sale) {
                $monthNumber = $sale->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'total_sales' => $sale->total_sales,
                ];
            }
        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){
            $sales = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(sale_value) as total_sales')
            )
                ->where('sales_rep_id', Auth::user()->id)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();

            $data = [];

            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];

            foreach ($sales as $sale) {
                $monthNumber = $sale->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'total_sales' => $sale->total_sales,
                ];
            }
        }

        return response()->json($data);

    }

    public function get_weekly_sales()
    {

        $currentDate = Carbon::now();

        $currentMonthStart = $currentDate->copy()->startOfMonth();

        $previousMonthStart = $currentMonthStart->copy()->subMonth()->startOfMonth();

        $weeklySales = [];

        if (Auth::user()->roles[0]->name == 'CountryManager') {

            for ($i = 0; $i < 4; $i++) {
                $currentWeekStart = $currentMonthStart->copy()->addWeeks($i)->startOfWeek();
                $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

                $previousWeekStart = $previousMonthStart->copy()->addWeeks($i)->startOfWeek();
                $previousWeekEnd = $previousWeekStart->copy()->endOfWeek();

                $currentWeekSales = Sale::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->sum('sale_value');

                $previousWeekSales = Sale::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->sum('sale_value');

                $weeklySales[] = [
                    'current_week' => [
                        'start_date' => $currentWeekStart->format('Y-m-d'),
                        'end_date' => $currentWeekEnd->format('Y-m-d'),
                        'total_sales' => $currentWeekSales,
                    ],
                    'previous_week' => [
                        'start_date' => $previousWeekStart->format('Y-m-d'),
                        'end_date' => $previousWeekEnd->format('Y-m-d'),
                        'total_sales' => $previousWeekSales,
                    ],
                ];
            }

        }elseif(Auth::user()->roles[0]->name == 'RegionalManager') {

            $region_Manager = User::where('regional_manager_id', Auth::user()->id)->pluck('id');

            for ($i = 0; $i < 4; $i++) {
                $currentWeekStart = $currentMonthStart->copy()->addWeeks($i)->startOfWeek();
                $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

                $previousWeekStart = $previousMonthStart->copy()->addWeeks($i)->startOfWeek();
                $previousWeekEnd = $previousWeekStart->copy()->endOfWeek();

                $currentWeekSales = Sale::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->whereIn('sales_rep_id',$region_Manager)->sum('sale_value');

                $previousWeekSales = Sale::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->whereIn('sales_rep_id',$region_Manager)->sum('sale_value');

                $weeklySales[] = [
                    'current_week' => [
                        'start_date' => $currentWeekStart->format('Y-m-d'),
                        'end_date' => $currentWeekEnd->format('Y-m-d'),
                        'total_sales' => $currentWeekSales,
                    ],
                    'previous_week' => [
                        'start_date' => $previousWeekStart->format('Y-m-d'),
                        'end_date' => $previousWeekEnd->format('Y-m-d'),
                        'total_sales' => $previousWeekSales,
                    ],
                ];
            }

        }else{

            $area_Manager = User::where('state_manager_id', Auth::user()->id)->pluck('id');

            for ($i = 0; $i < 4; $i++) {
                $currentWeekStart = $currentMonthStart->copy()->addWeeks($i)->startOfWeek();
                $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

                $previousWeekStart = $previousMonthStart->copy()->addWeeks($i)->startOfWeek();
                $previousWeekEnd = $previousWeekStart->copy()->endOfWeek();

                $currentWeekSales = Sale::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->whereIn('sales_rep_id',$area_Manager)->sum('sale_value');

                $previousWeekSales = Sale::whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])->whereIn('sales_rep_id',$area_Manager)->sum('sale_value');

                $weeklySales[] = [
                    'current_week' => [
                        'start_date' => $currentWeekStart->format('Y-m-d'),
                        'end_date' => $currentWeekEnd->format('Y-m-d'),
                        'total_sales' => $currentWeekSales,
                    ],
                    'previous_week' => [
                        'start_date' => $previousWeekStart->format('Y-m-d'),
                        'end_date' => $previousWeekEnd->format('Y-m-d'),
                        'total_sales' => $previousWeekSales,
                    ],
                ];
            }

        }

        return response()->json($weeklySales);

    }

    public function get_won_sales(){

        $totalRecords = DentalOffice::where('sales_rep_id', Auth::user()->id)->count();
        $won_closed = DentalOffice::where('sales_rep_id', Auth::user()->id)->where('purchase_product', 'Yes')->count();

        $percentage_contact_won = ($totalRecords > 0) ? ($won_closed / $totalRecords) * 100 : 0;

        $data = intval($percentage_contact_won);

        return response()->json($data);
    }

    public function get_reschedule_sales(){

        $totalRecords = DentalOffice::where('sales_rep_id', Auth::user()->id)->count();
        $reschedule = DentalOffice::where('sales_rep_id', Auth::user()->id)
            ->where('purchase_product', null)
            ->whereNotNull('follow_up_date')
            ->whereNotNull('contact_date')
            ->count();

        $percentage_contact_reschedule = ($totalRecords > 0) ? ($reschedule / $totalRecords) * 100 : 0;

        $data = intval($percentage_contact_reschedule);


        return response()->json($data);
    }

    public function get_schedule_sales(){

        $totalRecords = DentalOffice::where('sales_rep_id', Auth::user()->id)->count();
        $contact_schedule = DentalOffice::where('sales_rep_id', Auth::user()->id)
            ->where('purchase_product', null)
            ->whereNull('follow_up_date')
            ->whereNotNull('contact_date')
            ->count();

        $percentage_contact_schedule = ($totalRecords > 0) ? ($contact_schedule / $totalRecords) * 100 : 0;

        $data = intval($percentage_contact_schedule);

        return response()->json($data);
    }

    public function get_total_sale()
    {

        $total_sale_count = '';
        $total_sale = '';
        $currentYear = Carbon::now()->year;

        if (Auth::user()->roles[0]->name == 'CountryManager'){

            $total_sale = Sale::count('id');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)->count('id');

        }elseif (Auth::user()->roles[0]->name == 'RegionalManager'){

            $total_sale = Sale::where('regional_manager_id', Auth::user()->id)->count('id');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)->whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('regional_manager_id', Auth::user()->id);
            })->count('id');



        }elseif(Auth::user()->roles[0]->name == 'AreaManager'){

            $total_sale = Sale::where('state_manager_id', Auth::user()->id)->count('id');

            $total_sale_count = Sale::whereYear('created_at', $currentYear)->whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('state_manager_id', Auth::user()->id);
            })->count('id');



        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){

            $total_sale = Sale::where('sales_rep_id', Auth::user()->id)->count('id');
            $total_sale_count = Sale::where('sales_rep_id',Auth::user()->id)->whereYear('created_at', $currentYear)->count('id');

        }

        $percentage= ($total_sale_count / $total_sale) * 100;

        $data = [
            'percentage_value' => $percentage,
            'count' => $total_sale_count
        ];
        return response()->json($data);
    }

    public function get_subscriptions_sale()
    {
        $data = '';
        if (Auth::user()->roles[0]->name == 'CountryManager') {
            $subscribers = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN subscription = "Standard" THEN 1 ELSE 0 END) as standard_count'),
                DB::raw('SUM(CASE WHEN subscription = "Premium" THEN 1 ELSE 0 END) as premium_count')
            )
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();
            $data = [];
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];
            foreach ($subscribers as $subscriber) {
                $monthNumber = $subscriber->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'standard_count' => $subscriber->standard_count,
                    'premium_count' => $subscriber->premium_count,
                ];
            }
        }elseif(Auth::user()->roles[0]->name == 'RegionalManager') {
            $areaManager = User::where('regional_manager_id', Auth::user()->id)->pluck('id');
            $subscribers = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN subscription = "Standard" THEN 1 ELSE 0 END) as standard_count'),
                DB::raw('SUM(CASE WHEN subscription = "Premium" THEN 1 ELSE 0 END) as premium_count')
            )
                ->whereIn('sales_rep_id', $areaManager)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();
            $data = [];
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];
            foreach ($subscribers as $subscriber) {
                $monthNumber = $subscriber->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'standard_count' => $subscriber->standard_count,
                    'premium_count' => $subscriber->premium_count,
                ];
            }
        }elseif(Auth::user()->roles[0]->name == 'AreaManager') {
            $areaManager = User::where('state_manager_id', Auth::user()->id)->pluck('id');
            $subscribers = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN subscription = "Standard" THEN 1 ELSE 0 END) as standard_count'),
                DB::raw('SUM(CASE WHEN subscription = "Premium" THEN 1 ELSE 0 END) as premium_count')
            )
                ->whereIn('sales_rep_id', $areaManager)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();
            $data = [];
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];
            foreach ($subscribers as $subscriber) {
                $monthNumber = $subscriber->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'standard_count' => $subscriber->standard_count,
                    'premium_count' => $subscriber->premium_count,
                ];
            }
        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){
            $subscribers = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN subscription = "Standard" THEN 1 ELSE 0 END) as standard_count'),
                DB::raw('SUM(CASE WHEN subscription = "Premium" THEN 1 ELSE 0 END) as premium_count')
            )
                ->where('sales_rep_id', Auth::user()->id)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();
            $data = [];
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];
            foreach ($subscribers as $subscriber) {
                $monthNumber = $subscriber->month;
                $monthName = $monthNames[$monthNumber];
                $data[] = [
                    'month' => $monthName,
                    'standard_count' => $subscriber->standard_count,
                    'premium_count' => $subscriber->premium_count,
                ];
            }
        }
        return response()->json($data);
    }
}


