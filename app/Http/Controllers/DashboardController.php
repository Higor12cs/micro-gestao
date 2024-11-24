<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $salesData = Order::where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('
                SUM(total_price) as total_price,
                SUM(total_cost) as total_cost,
                COUNT(*) as order_count,
                AVG(total_price) as average_ticket
            ')
            ->first();

        $total_price = $salesData->total_price;
        $contributionMarginPercentage = $total_price > 0
            ? $salesData->total_cost / $total_price * 100
            : 0;
        $orderCount = $salesData->order_count;
        $averageTicket = $salesData->average_ticket;

        return view('dashboard.index', [
            'total_price' => $total_price,
            'contributionMarginPercentage' => $contributionMarginPercentage,
            'orderCount' => $orderCount,
            'averageTicket' => $averageTicket,
        ]);
    }
}
