<?php

namespace App\Listeners;

use App\Events\OrderItemDeleted;

class UpdateStockOnOrderItemDeleted
{
    public function handle(OrderItemDeleted $event)
    {
        $stock = $event->orderItem->product->stock;

        $stock->updateStock(
            $event->orderItem->quantity,
            'ORDER_ITEM_DELETED',
            'Item de Pedido Excluído',
            [
                'order_item_id' => $event->orderItem->id,
                'unit_cost' => $event->orderItem->unit_price,
                'total_cost' => $event->orderItem->total_price,
                'created_by' => $event->orderItem->created_by,
            ]
        );
    }
}
