<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{


    public function CalculateTotalRevenuInAMonth($month)
    {
        $total = Order::where('status', 'completed')
            ->whereYear('orders.created_at', '=', Carbon::parse($month)->year)
            ->whereMonth('orders.created_at', '=', Carbon::parse($month)->month)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->sum(DB::raw('products.price * orders.quantity'));
        $count = Order::where('status', 'completed')
            ->whereMonth('orders.created_at', Carbon::parse($month)->month)
            ->count();

        return [$total , $count];
    }
    public function CalculateTotalRevenue()
    {
        $total = Order::where('status', 'completed')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->sum(DB::raw('products.price * orders.quantity'));
        $count = Order::where('status', 'completed')->count();

        $TotalAndCountCurrentMonth = $this->CalculateTotalRevenuInAMonth(Carbon::now());
        $TotalAndCountLastMonth = $this->CalculateTotalRevenuInAMonth(Carbon::now()->subMonth());

        $ActiveSales = Order::whereIn('status' , ['confirmed' , 'pending'])->count();
        $ActiveSalesThisMonth = Order::whereIn('status' , ['confirmed' , 'pending' ,'completed'])
            ->whereYear('created_at' ,Carbon::now()->year)
            ->whereMonth('created_at' ,Carbon::now()->month)->count();

        $ActiveSalesLastMonth = Order::whereIn('status', ['completed'])
        ->whereYear('created_at' ,Carbon::now()->year)
        ->whereMonth('created_at' ,Carbon::now()->subMonth());

        $data = $this->GetUserSubscriptionForTwoMonth();
        $RevenueEveryMonth  = Order::selectRaw('YEAR(orders.created_at) as year, MONTH(orders.created_at) as month, SUM(products.price * orders.quantity) as total_revenue')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('status', 'completed')
            ->groupBy('year', 'month')
            ->get();


        return response()->json(['total'=>$total, 'count'=>$count , 'currentMonth'=>$TotalAndCountCurrentMonth , 'lastMonth'=>$TotalAndCountLastMonth , 'ActiveSales'=>$ActiveSales , 'Subscriptions'=>$data , 'RevenueEveryMonth'=>$RevenueEveryMonth]);
    }
//
//    public function GetAmountSallesOfThisMonth()
//
//    {
//
//    }

    private function countAnAction($month , $table = 'users')
    {
        $monthDate = Carbon::parse($month)->format('Y-m');

        $count = DB::table($table)
            ->whereYear('created_at', '=', Carbon::parse($month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($month)->month)
            ->count();

        return $count;
    }

    public function GetUserSubscriptionInCurrentMonth()
    {
        try {
            $count = $this->countAnAction(Carbon::now());

            return response()->json($count);
        }catch (\Exception $e) {
            return response()->json($e , 500);
        }
    }

    public function GetUserSubscriptionInMonth($month)
    {
        try {
            $count = $this->countAnAction($month);

            return response()->json($month);
        }catch (\Exception $e) {
            return response()->json($e , 500);
        }
    }

    public function GetUserSubscriptionForTwoMonth()
    {
        try {
            $CountMonth1 = $this->GetUserSubscriptionInCurrentMonth()->original;
            $CountMonth2 = $this->countAnAction(Carbon::now()->subRealMonth());

            return ['month1'=>$CountMonth1, 'month2'=>$CountMonth2];
        }catch (\Exception $e) {
            return $e;
        }
    }
}
