<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrderItemService
{
    public function create(Order $order, array $data): void
    {
        DB::transaction(function () use ($order, $data) {
            $product = Product::findOrFail($data['product_id']);
            $item = $order->items()->create([
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'unit_cost' => $product->cost_price,
                'total_cost' => $data['quantity'] * $product->cost_price,
                'unit_price' => $data['unit_price'],
                'total_price' => $data['quantity'] * $data['unit_price'],
                'created_by' => auth()->id(),
            ]);

            $this->updateStock($product->stock, -$item->quantity, 'ORDER_ITEM_CREATED', $item);
            $this->recalculateOrderTotal($order);
        });
    }

    public function delete(string $orderItemId): void
    {
        DB::transaction(function () use ($orderItemId) {
            $item = OrderItem::findOrFail($orderItemId);
            $order = $item->order;

            $this->updateStock($item->product->stock, $item->quantity, 'ORDER_ITEM_DELETED', $item);
            $item->delete();
            $this->recalculateOrderTotal($order);
        });
    }

    private function recalculateOrderTotal(Order $order): void
    {
        $order->update([
            'total_price' => $order->items()->sum('total_price'),
            'total_cost' => $order->items()->sum('total_cost'),
        ]);
    }

    private function updateStock(Stock $stock, float $quantity, string $type, OrderItem $item): void
    {
        $stock->updateStock($quantity, $type, $type, [
            'order_item_id' => $item->id,
            'unit_cost' => $item->unit_cost,
            'total_cost' => $item->total_cost,
            'unit_price' => $item->unit_price,
            'total_price' => $item->total_price,
            'created_by' => $item->created_by,
        ]);
    }
}
