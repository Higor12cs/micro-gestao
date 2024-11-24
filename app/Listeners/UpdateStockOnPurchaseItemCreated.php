<?php

namespace App\Listeners;

use App\Events\PurchaseItemCreated;

class UpdateStockOnPurchaseItemCreated
{
    public function handle(PurchaseItemCreated $event)
    {
        $stock = $event->purchaseItem->product->stock;

        $stock->updateStock(
            $event->purchaseItem->quantity,
            'increment',
            [
                'purchase_item_id' => $event->purchaseItem->id,
                'unit_cost' => $event->purchaseItem->unit_cost,
                'total_cost' => $event->purchaseItem->total_cost,
                'created_by' => $event->purchaseItem->created_by,
            ]
        );
    }
}
