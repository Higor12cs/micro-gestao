<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    use TenantAuthorization;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTable();
        }

        return view('orders.index');
    }

    public function store(OrderRequest $request)
    {
        $order = Order::create($request->validated());

        return to_route('orders.edit', $order->sequential)
            ->with('success', 'Compra criada com sucesso!');
    }

    public function show(string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        return view('orders.show', compact('order'));
    }

    public function edit(string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        return view('orders.edit', compact('order'));
    }

    public function update(OrderRequest $request, string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        $order->update($request->validated());

        return to_route('orders.show', $order->sequential)
            ->with('success', 'Compra atualizada com sucesso!');
    }

    public function destroy(string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        $order->delete();

        return to_route('orders.index')
            ->with('success', 'Compra deletada com sucesso!');
    }

    private function getDataTable()
    {
        $query = Order::query()
            ->with(['customer', 'receivables'])
            ->select('orders.*');

        return DataTables::eloquent($query)
            ->editColumn('sequential', fn ($order) => str_pad($order->sequential, 5, '0', STR_PAD_LEFT))
            ->editColumn('date', fn ($order) => $order->date->format('d/m/Y'))
            ->editColumn('total', fn ($order) => 'R$ '.number_format($order->total_price, 2, ',', '.'))
            ->addColumn('customer', fn ($order) => $order->customer->legal_name ?? $order->customer->first_name)
            ->addColumn('finished', fn ($order) => view('partials.bool', ['bool' => $order->hasReceivables()]))
            ->addColumn('actions', fn ($order) => view('partials.actions', [
                'id' => $order->id,
                'entity' => 'orders',
                'modal' => false,
                'sequential' => $order->sequential,
                'edit' => ! $order->hasReceivables(),
            ]))
            ->make(true);
    }

    private function getOrderBySequential(string $sequential): Order
    {
        return Order::query()
            ->where('sequential', $sequential)
            ->firstOrFail();
    }
}
