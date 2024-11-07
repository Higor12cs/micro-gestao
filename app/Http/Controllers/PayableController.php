<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PayableController extends Controller
{
    public function index()
    {
        $query = Payable::query()
            ->with('supplier')
            ->where('tenant_id', auth()->user()->tenant->id)
            ->select('payables.*');

        return DataTables::make($query)
            ->editColumn('sequential', function ($payable) {
                return str_pad($payable->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->addColumn('supplier', function ($payable) {
                return $payable->supplier->legal_name ?? $payable->supplier->first_name;
            })
            ->editColumn('due_date', function ($payable) {
                return $payable->due_date->format('d/m/Y');
            })
            ->addColumn('paid', function ($payable) {
                return view('partials.bool', ['bool' => $payable->paid]);
            })
            ->editColumn('amount', function ($payable) {
                return number_format($payable->amount, 2, '.', ',');
            })
            ->addColumn('actions', function ($payable) {
                return view('partials.actions', [
                    'id' => $payable->id,
                    'entity' => 'payables',
                    'modal' => true,
                    'sequential' => $payable->sequential,
                ]);
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;

        $payable = Payable::create($data);

        return to_route('payables.edit', $payable);
    }

    public function show(string $sequential)
    {
        $payable = Payable::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return response()->json($payable);
    }

    public function edit(string $sequential)
    {
        $payable = Payable::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('payables.edit', compact('payable'));
    }

    public function update(Request $request, string $sequential)
    {
        $payable = Payable::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $payable->update($request->validated());

        return response()->json($payable);
    }

    public function destroy(string $sequential)
    {
        $payable = Payable::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $payable->delete();

        return response()->json(null, 204);
    }

    public function payables(string $sequential)
    {
        $payable = Payable::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('payables.payables', compact('payable'));
    }
}
