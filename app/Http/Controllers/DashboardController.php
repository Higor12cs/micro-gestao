<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $salesData = Order::where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('
                SUM(total_price) as total_revenue,
                (SUM(total_price - total_cost - discount - freight) / SUM(total_price)) * 100 as contribution_margin_percentage,
                COUNT(*) as order_count,
                AVG(total_price) as average_ticket
            ')
            ->first();

        $totalRevenue = $salesData->total_revenue;
        $contributionMarginPercentage = $salesData->contribution_margin_percentage;
        $orderCount = $salesData->order_count;
        $averageTicket = $salesData->average_ticket;

        return view('dashboard.index', [
            'totalRevenue' => $totalRevenue,
            'contributionMarginPercentage' => $contributionMarginPercentage,
            'orderCount' => $orderCount,
            'averageTicket' => $averageTicket,
        ]);
    }
}
