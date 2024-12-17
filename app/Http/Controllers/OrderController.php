<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use TenantAuthorization;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->orderService->getDataTable();
        }

        return view('orders.index');
    }

    public function store(OrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());

        return to_route('orders.edit', $order->sequential)
            ->with('success', 'Pedido criado com sucesso!');
    }

    public function show(string $sequential)
    {
        $order = $this->orderService->getOrderBySequential($sequential);

        return view('orders.show', compact('order'));
    }

    public function edit(string $sequential)
    {
        $order = $this->orderService->getOrderBySequential($sequential);

        return view('orders.edit', compact('order'));
    }

    public function update(OrderRequest $request, string $sequential)
    {
        $this->orderService->updateOrder($sequential, $request->validated());

        return to_route('orders.show', $sequential)
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy(string $sequential)
    {
        $this->orderService->deleteOrder($sequential);

        return to_route('orders.index')
            ->with('success', 'Pedido deletado com sucesso!');
    }
}
