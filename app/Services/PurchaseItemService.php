<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class PurchaseItemService
{
    public function create(Purchase $purchase, array $data): void
    {
        DB::transaction(function () use ($purchase, $data) {
            $product = Product::findOrFail($data['product_id']);
            $item = $purchase->items()->create([
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'],
                'total_cost' => $data['quantity'] * $data['unit_cost'],
                'created_by' => auth()->id(),
            ]);

            $this->updateStock($product->stock, $item->quantity, 'PURCHASE_ITEM_CREATED', $item);
            $this->recalculatePurchaseTotal($purchase);
        });
    }

    public function delete(string $purchaseItemId): void
    {
        DB::transaction(function () use ($purchaseItemId) {
            $item = PurchaseItem::findOrFail($purchaseItemId);
            $purchase = $item->purchase;

            $this->updateStock($item->product->stock, -$item->quantity, 'PURCHASE_ITEM_DELETED', $item);
            $item->delete();
            $this->recalculatePurchaseTotal($purchase);
        });
    }

    private function recalculatePurchaseTotal(Purchase $purchase): void
    {
        $purchase->update([
            'total' => $purchase->items()->sum('total_cost'),
        ]);
    }

    private function updateStock(Stock $stock, float $quantity, string $type, PurchaseItem $item): void
    {
        $stock->updateStock($quantity, $type, $type, [
            'purchase_item_id' => $item->id,
            'unit_cost' => $item->unit_cost,
            'total_cost' => $item->total_cost,
            'created_by' => $item->created_by,
        ]);
    }
}
