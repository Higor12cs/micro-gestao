<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PurchaseController extends Controller
{
    public function index()
    {
        $query = Purchase::query()
            ->with(['supplier', 'payables'])
            ->where('tenant_id', auth()->user()->tenant->id)
            ->select('purchases.*');

        return DataTables::make($query)
            ->editColumn('sequential', function ($purchase) {
                return str_pad($purchase->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->editColumn('date', function ($purchase) {
                return $purchase->date->format('d/m/Y');
            })
            ->editColumn('total', function ($purchase) {
                return 'R$ ' . number_format($purchase->total, 2, ',', '.');
            })
            ->addColumn('supplier', function ($purchase) {
                return $purchase->supplier->legal_name ?? $purchase->supplier->first_name;
            })
            ->addColumn('finished', function ($purchase) {
                return view('partials.bool', [
                    'bool' => $purchase->hasPayables(),
                ]);
            })
            ->addColumn('actions', function ($purchase) {
                return view('partials.actions', [
                    'id' => $purchase->id,
                    'entity' => 'purchases',
                    'modal' => false,
                    'sequential' => $purchase->sequential,
                    'edit' => !$purchase->hasPayables(),
                ]);
            })
            ->make(true);
    }

    public function store(PurchaseRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;

        $purchase = Purchase::create($data);

        return to_route('purchases.edit', $purchase);
    }

    public function show(string $sequential)
    {
        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('purchases.show', compact('purchase'));
    }

    public function edit(string $sequential)
    {
        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('purchases.edit', compact('purchase'));
    }

    public function update(PurchaseRequest $request, string $sequential)
    {
        abort(501);
    }

    public function destroy(string $sequential)
    {
        abort(501);
        // $purchase = Purchase::query()
        //     ->where('tenant_id', auth()->user()->tenant->id)
        //     ->where('sequential', $sequential)
        //     ->firstOrFail();

        // $purchase->delete();

        // return response()->json(null, 204);
    }

    public function purchasePayables(string $sequential)
    {
        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('purchases.payables', compact('purchase'));
    }

    public function storePayables(Request $request, string $sequential)
    {
        if (! $request->has('payables')) {
            return back()->withErrors(['payables' => 'É necessário cadastrar ao menos uma parcela.']);
        }

        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        if ($purchase->hasPayables()) {
            return back()->withErrors(['payables' => 'As parcelas já foram cadastradas.']);
        }

        $validated = $request->validate([
            'payables.*.due_date' => 'required|date|after_or_equal:today',
            'payables.*.amount' => 'required|numeric|min:0.01',
        ], [
            'payables.*.due_date.required' => 'O campo data de vencimento é obrigatório.',
            'payables.*.due_date.date' => 'O campo data de vencimento deve ser uma data válida.',
            'payables.*.due_date.after_or_equal' => 'O campo data de vencimento deve ser uma data igual ou posterior a hoje.',
            'payables.*.amount.required' => 'O campo valor é obrigatório.',
            'payables.*.amount.numeric' => 'O campo valor deve ser um número.',
            'payables.*.amount.min' => 'O campo valor deve ser no mínimo R$ 0,01.',
        ]);

        if (array_sum(array_column($request->payables, 'amount')) != $purchase->total) {
            return back()->withInput()->withErrors(['payables' => 'A soma dos valores das parcelas deve ser igual ao valor total da compra.']);
        }

        $payables = collect($validated['payables'])->map(function ($payable) use ($purchase) {
            return array_merge($payable, [
                'tenant_id' => $purchase->tenant_id,
                'supplier_id' => $purchase->supplier_id,
                'created_by' => auth()->id(),
            ]);
        })->toArray();

        $purchase->payables()->createMany($payables);

        return to_route('purchases.index');
    }
}
