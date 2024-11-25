<?php

namespace App\Listeners;

use App\Events\PurchaseItemDeleted;

class UpdateStockOnPurchaseItemDeleted
{
    public function handle(PurchaseItemDeleted $event)
    {
        $stock = $event->purchaseItem->product->stock;

        $stock->updateStock(
            -$event->purchaseItem->quantity,
            'PURCHASE_ITEM_DELETED',
            'Item de Compra Excluído',
            [
                'purchase_item_id' => $event->purchaseItem->id,
                'unit_cost' => $event->purchaseItem->unit_cost,
                'total_cost' => $event->purchaseItem->total_cost,
                'created_by' => $event->purchaseItem->created_by,
            ]
        );
    }
}
