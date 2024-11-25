<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    use TenantAuthorization;

    public function index(Purchase $purchase)
    {
        $this->authorizeTenantAccess($purchase);

        $items = $purchase->items()->with('product')->get();

        return response()->json($items);
    }

    public function store(Request $request, Purchase $purchase)
    {
        $this->authorizeTenantAccess($purchase);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_cost' => 'required|numeric|min:0.01',
        ]);

        $purchase->items()->create(array_merge($validated, [
            'total_cost' => $validated['quantity'] * $validated['unit_cost'],
            'created_by' => auth()->id(),
        ]));

        $this->updatePurchaseTotal($purchase);

        return response()->json(['success' => true]);
    }

    public function destroy(Purchase $purchase, $itemId)
    {
        $this->authorizeTenantAccess($purchase);

        $purchase->items()->findOrFail($itemId)->delete();
        $this->updatePurchaseTotal($purchase);

        return response()->json(['success' => true]);
    }

    private function updatePurchaseTotal(Purchase $purchase)
    {
        $purchase->update(['total' => $purchase->items()->sum('total_cost')]);
    }
}
