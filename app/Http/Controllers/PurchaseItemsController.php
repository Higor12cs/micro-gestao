<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseItemsController extends Controller
{
    public function index(Purchase $purchase)
    {
        $items = $purchase->items()->with('product')->get();

        return response()->json($items);
    }

    public function store(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $purchase->items()->create(array_merge($validated, [
            'total_price' => $validated['quantity'] * $validated['unit_price'],
            'tenant_id' => auth()->user()->tenant->id,
            'created_by' => auth()->id(),
        ]));

        $this->updatePurchaseTotal($purchase);

        return response()->json(['success' => true]);
    }

    public function destroy(Purchase $purchase, $itemId)
    {
        $purchase->items()->findOrFail($itemId)->delete();
        $this->updatePurchaseTotal($purchase);

        return response()->json(['success' => true]);
    }

    private function updatePurchaseTotal(Purchase $purchase)
    {
        $purchase->update(['total' => $purchase->items()->sum('total_price')]);
    }
}
