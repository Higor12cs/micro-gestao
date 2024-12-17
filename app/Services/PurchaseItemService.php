<?php

namespace App\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseItemService
{
    public function getItemsForPurchase(Purchase $purchase)
    {
        return $purchase->items()->with('product')->get();
    }

    public function addItemToPurchase(Purchase $purchase, array $data)
    {
        $validated = $this->validatePurchaseItemData($data);

        DB::transaction(function () use ($purchase, $validated) {
            $purchase->items()->create(array_merge($validated, [
                'total_cost' => $validated['quantity'] * $validated['unit_cost'],
                'created_by' => auth()->id(),
            ]));

            $this->updatePurchaseTotal($purchase);
        });
    }

    public function removeItemFromPurchase(Purchase $purchase, string $itemId)
    {
        DB::transaction(function () use ($purchase, $itemId) {
            $item = $purchase->items()->findOrFail($itemId);
            $item->delete();

            $this->updatePurchaseTotal($purchase);
        });
    }

    private function updatePurchaseTotal(Purchase $purchase)
    {
        $purchase->update(['total' => $purchase->items()->sum('total_cost')]);
    }

    private function validatePurchaseItemData(array $data): array
    {
        return validator($data, [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_cost' => 'required|numeric|min:0.01',
        ])->validate();
    }
}
