<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchasePayableController extends Controller
{
    public function index(string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        return view('purchases.payables', compact('purchase'));
    }

    public function store(Request $request, string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        $request->merge([
            'payables' => collect($request->input('payables'))->map(function ($payable) {
                $payable['amount'] = (float) str_replace(['.', ','], ['', '.'], $payable['amount']);

                return $payable;
            })->toArray(),
        ]);

        $request->validate([
            'payables.*.due_date' => 'required|date|after_or_equal:today',
            'payables.*.amount' => 'required|numeric|min:0.01',
        ]);

        $payables = collect($request->input('payables'))->map(fn ($payable) => array_merge($payable, [
            'tenant_id' => $purchase->tenant_id,
            'supplier_id' => $purchase->supplier_id,
            'created_by' => auth()->id(),
        ]));

        $totalAmount = $payables->sum('amount');

        if ($totalAmount != $purchase->total) {
            return back()->withErrors(['O valor das parcelas deve ser o igual ao valor total da compra.']);
        }

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
