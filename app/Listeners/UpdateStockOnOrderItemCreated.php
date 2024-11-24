<?php

namespace App\Listeners;

use App\Events\OrderItemCreated;

class UpdateStockOnOrderItemCreated
{
    public function handle(OrderItemCreated $event)
    {
        $stock = $event->orderItem->product->stock;

        $stock->updateStock(
            -$event->orderItem->quantity,
            'order_item_created',
            [
                'order_item_id' => $event->orderItem->id,
                'unit_cost' => $event->orderItem->unit_price,
                'total_cost' => $event->orderItem->total_price,
                'created_by' => $event->orderItem->created_by,
            ]
        );
    }
}
