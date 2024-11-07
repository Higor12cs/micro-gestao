<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use Yajra\DataTables\DataTables;

class PurchaseController extends Controller
{
    public function index()
    {
        $query = Purchase::query()
            ->with('supplier')
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
                return number_format($purchase->total, 2, '.', ',');
            })
            ->addColumn('supplier', function ($purchase) {
                return $purchase->supplier->legal_name ?? $purchase->supplier->first_name;
            })
            ->addColumn('finished', function ($purchase) {
                return view('partials.bool', ['bool' => $purchase->hasPayables()]);
            })
            ->addColumn('actions', function ($purchase) {
                return view('partials.actions', [
                    'id' => $purchase->id,
                    'entity' => 'purchases',
                    'modal' => false,
                    'sequential' => $purchase->sequential,
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

        return response()->json($purchase);
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
        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $data = $request->validated();
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $purchase->update($data);

        return response()->json($purchase);
    }

    public function destroy(string $sequential)
    {
        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $purchase->delete();

        return response()->json(null, 204);
    }

    public function payables(string $sequential)
    {
        $purchase = Purchase::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('purchases.payables', compact('purchase'));
    }
}
