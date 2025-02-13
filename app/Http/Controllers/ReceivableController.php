<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceivableRequest;
use App\Models\Receivable;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReceivableController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTable();
        }

        return view('receivables.index');
    }

    public function store(ReceivableRequest $request)
    {
        $receivable = Receivable::create($request->validated());

        return to_route('receivables.edit', ['sequential' => $receivable->sequential]);
    }

    public function show(Receivable $receivable)
    {
        $this->authorizeTenantAccess($receivable);

        return response()->json($receivable);
    }

    public function edit(Receivable $receivable)
    {
        $this->authorizeTenantAccess($receivable);

        return view('receivables.edit', compact('receivable'));
    }

    public function update(ReceivableRequest $request, Receivable $receivable)
    {
        $this->authorizeTenantAccess($receivable);

        $receivable->update($request->validated());

        return response()->json($receivable);
    }

    public function destroy(Receivable $receivable)
    {
        $this->authorizeTenantAccess($receivable);

        $receivable->delete();

        return response()->json(null, 204);
    }

    private function getDataTable()
    {
        $query = Receivable::with('customer')
            ->select('receivables.*');

        return DataTables::of($query)
            ->editColumn('sequential', fn ($receivable) => str_pad($receivable->sequential, 5, '0', STR_PAD_LEFT))
            ->addColumn('customer', fn ($receivable) => $receivable->customer->legal_name ?? $receivable->customer->first_name)
            ->editColumn('due_date', fn ($receivable) => $receivable->due_date->format('d/m/Y'))
            ->addColumn('paid', fn ($receivable) => view('partials.bool', ['bool' => $receivable->paid]))
            ->editColumn('amount', fn ($receivable) => 'R$ '.number_format($receivable->amount, 2, ',', '.'))
            ->addColumn('actions', fn ($receivable) => view('partials.actions', [
                'id' => $receivable->id,
                'entity' => 'receivables',
                'modal' => true,
                'sequential' => $receivable->sequential,
            ]))
            ->make(true);
    }
}
