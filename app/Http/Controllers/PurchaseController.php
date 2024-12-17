<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Services\PurchaseService;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    use TenantAuthorization;

    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->purchaseService->getDataTable();
        }

        return view('purchases.index');
    }

    public function store(PurchaseRequest $request)
    {
        $purchase = $this->purchaseService->create($request->validated());

        return to_route('purchases.edit', $purchase->sequential)
            ->with('success', 'Compra criada com sucesso!');
    }

    public function show(string $sequential)
    {
        $purchase = $this->purchaseService->findBySequential($sequential);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(string $sequential)
    {
        $purchase = $this->purchaseService->findBySequential($sequential);

        return view('purchases.edit', compact('purchase'));
    }

    public function update(PurchaseRequest $request, string $sequential)
    {
        $purchase = $this->purchaseService->update($sequential, $request->validated());

        return to_route('purchases.show', $purchase->sequential)
            ->with('success', 'Compra atualizada com sucesso!');
    }

    public function destroy(string $sequential)
    {
        $this->purchaseService->delete($sequential);

        return to_route('purchases.index')
            ->with('success', 'Compra deletada com sucesso!');
    }
}
