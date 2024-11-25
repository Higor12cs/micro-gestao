<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    use TenantAuthorization;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTable();
        }

        return view('purchases.index');
    }

    public function store(PurchaseRequest $request)
    {
        $purchase = Purchase::create($request->validated());

        return to_route('purchases.edit', $purchase->sequential)
            ->with('success', 'Compra criada com sucesso!');
    }

    public function show(string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        return view('purchases.edit', compact('purchase'));
    }

    public function update(PurchaseRequest $request, string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        $purchase->update($request->validated());

        return to_route('purchases.show', $purchase->sequential)
            ->with('success', 'Compra atualizada com sucesso!');
    }

    public function destroy(string $sequential)
    {
        $purchase = $this->getPurchaseBySequential($sequential);

        $purchase->delete();

        return to_route('purchases.index')
            ->with('success', 'Compra deletada com sucesso!');
    }

    private function getDataTable()
    {
        $query = Purchase::query()
            ->with(['supplier', 'payables'])
            ->select('purchases.*');

        return DataTables::eloquent($query)
            ->editColumn('sequential', fn ($purchase) => str_pad($purchase->sequential, 5, '0', STR_PAD_LEFT))
            ->editColumn('date', fn ($purchase) => $purchase->date->format('d/m/Y'))
            ->editColumn('total', fn ($purchase) => 'R$ '.number_format($purchase->total, 2, ',', '.'))
            ->addColumn('supplier', fn ($purchase) => $purchase->supplier->legal_name ?? $purchase->supplier->first_name)
            ->addColumn('finished', fn ($purchase) => view('partials.bool', ['bool' => $purchase->hasPayables()]))
            ->addColumn('actions', fn ($purchase) => view('partials.actions', [
                'id' => $purchase->id,
                'entity' => 'purchases',
                'modal' => false,
                'sequential' => $purchase->sequential,
                'edit' => ! $purchase->hasPayables(),
            ]))
            ->make(true);
    }

    private function getPurchaseBySequential(string $sequential): Purchase
    {
        return Purchase::query()
            ->where('sequential', $sequential)
            ->firstOrFail();
    }
}
