<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderReceivableController extends Controller
{
    public function index(string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        return view('orders.receivables', compact('order'));
    }

    public function store(Request $request, string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        $request->validate([
            'receivables.*.due_date' => 'required|date|after_or_equal:today',
            'receivables.*.amount' => 'required|string',
        ]);

        $receivables = collect($request->input('receivables'))->map(function ($receivable) {
            $receivable['amount'] = (float) str_replace(['.', ','], ['', '.'], preg_replace('/[^\d,.-]/', '', $receivable['amount']));

            return $receivable;
        });

        $totalAmount = $receivables->sum('amount');

        if ($totalAmount != $order->total_price) {
            return back()->withErrors(['O valor das parcelas deve ser o igual ao valor total do pedido.']);
        }

        $receivables = $receivables->map(fn ($receivable) => array_merge($receivable, [
            'customer_id' => $order->customer_id,
            'created_by' => auth()->id(),
        ]));

        $order->receivables()->createMany($receivables->toArray());

        return to_route('orders.index')
            ->with('success', 'Parcelas adicionadas com sucesso!');
    }

    private function getOrderBySequential(string $sequential): Order
    {
        return Order::query()
            ->where('sequential', $sequential)
            ->firstOrFail();
    }
}
