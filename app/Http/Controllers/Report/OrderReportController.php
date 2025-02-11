<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    public function report(Request $request)
    {
        if (!$request->has('date')) {
            return back()->withErrors(['date' => 'O período de datas é obrigatório.']);
        }

        $dates = explode(' - ', $request->date);

        try {
            $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
        } catch (\Exception $e) {
            return back()->withErrors(['date' => 'Formato de data inválido.']);
        }

        $orders = Order::whereBetween('date', [$startDate, $endDate])->get();

        $pdf = Pdf::loadView('reports.orders.report', [
            'orders' => $orders,
            'startDate' => $dates[0],
            'endDate' => $dates[1],
        ]);

        return $pdf->stream('Relatorio.pdf');
    }
}
