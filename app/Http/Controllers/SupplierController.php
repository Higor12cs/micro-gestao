<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $suppliers = Supplier::where('tenant_id', auth()->user()->tenant->id)
            ->where(function ($query) use ($request) {
                $query->where('first_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('last_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('legal_name', 'like', "%{$request->input('search')}%");
            })
            ->limit(10)
            ->get()
            ->map(fn ($supplier) => [
                'id' => $supplier->id,
                'text' => $supplier->first_name,
            ]);

        return response()->json($suppliers);
    }

    public function index()
    {
        $query = Supplier::where('tenant_id', auth()->user()->tenant->id);

        return DataTables::of($query)
            ->editColumn('sequential', fn ($supplier) => str_pad($supplier->sequential, 5, '0', STR_PAD_LEFT))
            ->addColumn('actions', fn ($supplier) => view('partials.actions', [
                'id' => $supplier->id,
                'entity' => 'suppliers',
            ]))
            ->make(true);
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;

        $supplier = Supplier::create($data);

        return response()->json($supplier, 201);
    }

    public function show(string $sequential)
    {
        $supplier = Supplier::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        return response()->json($supplier);
    }

    public function update(SupplierRequest $request, string $sequential)
    {
        $supplier = Supplier::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $supplier->update($request->validated());

        return response()->json($supplier);
    }

    public function destroy(string $sequential)
    {
        $supplier = Supplier::where('tenant_id', auth()->user()->tenant->id)
            ->where('sequential', $sequential)
            ->firstOrFail();

        $supplier->delete();

        return response()->json(null, 204);
    }
}
