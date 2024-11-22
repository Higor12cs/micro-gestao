<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PayableController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = Payable::with('supplier')
                ->where('tenant_id', auth()->user()->tenant->id)
                ->select('payables.*');

            return DataTables::of($query)
                ->editColumn('sequential', fn ($payable) => str_pad($payable->sequential, 5, '0', STR_PAD_LEFT))
                ->addColumn('supplier', fn ($payable) => $payable->supplier->legal_name ?? $payable->supplier->first_name)
                ->editColumn('due_date', fn ($payable) => $payable->due_date->format('d/m/Y'))
                ->addColumn('paid', fn ($payable) => view('partials.bool', ['bool' => $payable->paid]))
                ->editColumn('amount', fn ($payable) => number_format($payable->amount, 2, '.', ','))
                ->addColumn('actions', fn ($payable) => view('partials.actions', [
                    'id' => $payable->id,
                    'entity' => 'payables',
                    'modal' => true,
                    'sequential' => $payable->sequential,
                ]))
                ->make(true);
        }

        return view('payables.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);
        $data['tenant_id'] = auth()->user()->tenant->id;

        $payable = Payable::create($data);

        return to_route('payables.edit', ['sequential' => $payable->sequential]);
    }

    public function show(string $sequential)
    {
        $payable = Payable::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return response()->json($payable);
    }

    public function edit(string $sequential)
    {
        $payable = Payable::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('payables.edit', compact('payable'));
    }

    public function update(Request $request, string $sequential)
    {
        $payable = Payable::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $payable->update($data);

        return response()->json($payable);
    }

    public function destroy(string $sequential)
    {
        $payable = Payable::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $payable->delete();

        return response()->json(null, 204);
    }

    public function payables(string $sequential)
    {
        $payable = Payable::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return view('payables.payables', compact('payable'));
    }
}
