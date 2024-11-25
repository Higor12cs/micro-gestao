<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    use TenantAuthorization;

    public function index(Order $order)
    {
        $this->authorizeTenantAccess($order);

        $items = $order->items()->with('product')->get();

        return response()->json($items);
    }

    public function store(Request $request, Order $order)
    {
        $this->authorizeTenantAccess($order);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $order->items()->create(array_merge($validated, [
            'unit_cost' => $product->cost_price,
            'total_cost' => $validated['quantity'] * $product->cost_price,
            'total_price' => $validated['quantity'] * $validated['unit_price'],
            'created_by' => auth()->id(),
        ]));

        $this->recalculateOrderTotal($order);

        return response()->json(['success' => true]);
    }

    public function destroy(Order $order, OrderItem $orderItem)
    {
        $this->authorizeTenantAccess($orderItem->order);

        $orderItem->delete();

        $this->recalculateOrderTotal($order);

        return response()->json(['success' => true]);
    }

    private function recalculateOrderTotal(Order $order)
    {
        $order->update([
            'total_price' => $order->items()->sum('total_price'),
            'total_cost' => $order->items()->sum('total_cost'),
        ]);
    }
}
