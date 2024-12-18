<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderItemService;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    use TenantAuthorization;

    protected OrderItemService $service;

    public function __construct(OrderItemService $service)
    {
        $this->service = $service;
    }

    public function index(Order $order)
    {
        $this->authorizeTenantAccess($order);

        return response()->json($order->items()->with('product')->get());
    }

    public function store(Request $request, Order $order)
    {
        $this->authorizeTenantAccess($order);

        $this->service->create($order, $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy(Order $order, string $orderItemId)
    {
        $this->authorizeTenantAccess($order);

        $this->service->delete($orderItemId);

        return response()->json(['success' => true]);
    }
}
