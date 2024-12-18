<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\PurchaseItemService;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    use TenantAuthorization;

    private PurchaseItemService $service;

    public function __construct(PurchaseItemService $service)
    {
        $this->service = $service;
    }

    public function index(Purchase $purchase)
    {
        $this->authorizeTenantAccess($purchase);

        return response()->json($purchase->items()->with('product')->get());
    }

    public function store(Purchase $purchase, Request $request)
    {
        $this->authorizeTenantAccess($purchase);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $this->service->create($purchase, $validated);

        return response()->json(['success' => true]);
    }

    public function destroy(Purchase $purchase, PurchaseItem $purchaseItem)
    {
        $this->authorizeTenantAccess($purchase);

        $this->service->delete($purchaseItem->id);

        return response()->json(['success' => true]);
    }
}
