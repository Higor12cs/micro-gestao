<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchasePayablesController extends Controller
{
    public function index(string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        return view('purchases.payables', compact('purchase'));
    }

    public function store(Request $request, string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        $request->validate([
            'payables.*.due_date' => 'required|date|after_or_equal:today',
            'payables.*.amount' => 'required|numeric|min:0.01',
        ]);

        $payables = collect($request->input('payables'))->map(fn ($payable) => array_merge($payable, [
            'tenant_id' => $purchase->tenant_id,
            'supplier_id' => $purchase->supplier_id,
            'created_by' => auth()->id(),
        ]));

        $purchase->payables()->createMany($payables->toArray());

        return to_route('purchases.index')
            ->with('success', 'Parcelas adicionadas com sucesso!');
    }

    private function getPurchaseBySequential(string $sequential): Purchase
    {
        return Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();
    }
}
