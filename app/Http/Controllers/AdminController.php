<?php

namespace App\Http\Controllers;

use App\Models\DentalOffice;
use App\Models\Sale;
use App\Models\User;
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

        if(Auth::user()->roles[0]->name == 'AreaManager'){

            $total_sale = Sale::whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('state_manager_id', Auth::user()->id);
            })->sum('sale_value');

            $total_sale_count = Sale::whereIn('sales_rep_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->where('state_manager_id', Auth::user()->id);
            })->count('id');

            $dental_offices = DentalOffice::all();

        }elseif (Auth::user()->roles[0]->name == 'SalesRepresentative'){

            $total_sale = Sale::where('sales_rep_id',Auth::user()->id)->sum('sale_value');
            $total_sale_count = Sale::where('sales_rep_id',Auth::user()->id)->count('id');

            $dental_offices = DentalOffice::all();

        }

        return view('dashboard', compact('total_sale','total_sale_count', 'dental_offices'));
    }

    public function get_response()
    {
        $data = '';
        if(Auth::user()->roles[0]->name == 'AreaManager') {

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

    public function get_currentYear_sales()
    {
        $formattedData = '';
        if(Auth::user()->roles[0]->name == 'AreaManager') {
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
        if(Auth::user()->roles[0]->name == 'AreaManager') {
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
}
