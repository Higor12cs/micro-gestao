<?php

namespace App\Listeners;

use App\Events\PurchaseItemDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStockOnPurchaseItemDeleted
{
    public function handle(PurchaseItemDeleted $event)
    {
        $stock = $event->purchaseItem->product->stock;

        $stock->updateStock(
            -$event->purchaseItem->quantity,
            'decrement',
            [
                'purchase_item_id' => $event->purchaseItem->id,
                'unit_cost' => $event->purchaseItem->unit_cost,
                'total_cost' => $event->purchaseItem->total_cost,
                'created_by' => $event->purchaseItem->created_by,
            ]
        );
    }
}