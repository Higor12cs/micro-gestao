<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderReceivablesController extends Controller
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
            'receivables.*.amount' => 'required|numeric|min:0.01',
        ]);

        $receivables = collect($request->input('receivables'))->map(fn ($receivable) => array_merge($receivable, [
            'tenant_id' => $order->tenant_id,
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
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();
    }
}
