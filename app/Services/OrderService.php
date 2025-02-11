<?php

namespace App\Services;

use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function updateOrder(string $sequential, array $data)
    {
        $order = $this->getOrderBySequential($sequential);

        $order->update($data);
    }

    public function deleteOrder(string $sequential)
    {
        $order = $this->getOrderBySequential($sequential);

        $order->delete();
    }

    public function getOrderBySequential(string $sequential): Order
    {
        return Order::query()
            ->where('sequential', $sequential)
            ->firstOrFail();
    }

    public function getDataTable()
    {
        $query = Order::query()
            ->with(['customer', 'receivables'])
            ->select('orders.*');

        return DataTables::eloquent($query)
            ->editColumn('sequential', fn($order) => str_pad($order->sequential, 5, '0', STR_PAD_LEFT))
            ->editColumn('date', fn($order) => $order->date->format('d/m/Y'))
            ->editColumn('total', fn($order) => 'R$ ' . number_format($order->total_price, 2, ',', '.'))
            ->addColumn('customer', fn($order) => $order->customer->legal_name ?? $order->customer->first_name)
            ->addColumn('finished', fn($order) => view('partials.bool', ['bool' => $order->hasReceivables()]))
            ->addColumn('actions', fn($order) => view('partials.actions', [
                'id' => $order->sequential,
                'entity' => 'orders',
                'modal' => false,
                'sequential' => $order->sequential,
                'edit' => ! $order->hasReceivables(),
            ]))
            ->make(true);
    }
}
