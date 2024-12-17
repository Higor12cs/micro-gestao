<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\PurchaseItemService;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    use TenantAuthorization;

    protected PurchaseItemService $purchaseItemService;

    public function __construct(PurchaseItemService $purchaseItemService)
    {
        $this->purchaseItemService = $purchaseItemService;
    }

    public function index(Purchase $purchase)
    {
        $this->authorizeTenantAccess($purchase);

        $items = $this->purchaseItemService->getItemsForPurchase($purchase);

        return response()->json($items);
    }

    public function store(Request $request, Purchase $purchase)
    {
        $this->authorizeTenantAccess($purchase);

        $this->purchaseItemService->addItemToPurchase($purchase, $request->all());

        return response()->json(['success' => true]);
    }

    public function destroy(Purchase $purchase, string $itemId)
    {
        $this->authorizeTenantAccess($purchase);

        $this->purchaseItemService->removeItemFromPurchase($purchase, $itemId);

        return response()->json(['success' => true]);
    }
}
