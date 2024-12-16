<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    public function report(Request $request)
    {
        $dates = explode(' - ', $request->date);

        $orders = Order::whereBetween('date', [
            now()->parse($dates[0])->startOfDay(),
            now()->parse($dates[1])->endOfDay()
        ])->get();

        $pdf = Pdf::loadView('reports.orders.report', compact('orders'));

        return $pdf->stream('Relatório de Pedidos.pdf');
    }
}
