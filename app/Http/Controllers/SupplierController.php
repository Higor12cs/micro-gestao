<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    public function search(Request $request)
    {
        $suppliers = Supplier::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where(function ($query) use ($request) {
                $query->where('first_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('last_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('legal_name', 'like', "%{$request->input('search')}%");
            })
            ->limit(10)
            ->get();

        $suppliers = $suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'text' => $supplier->first_name,
            ];
        });

        return response()->json($suppliers);
    }

    public function index()
    {
        $query = Supplier::query()
            ->where('tenant_id', auth()->user()->tenant->id);

        return DataTables::make($query)
            ->editColumn('sequential', function ($supplier) {
                return str_pad($supplier->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->addColumn('actions', function ($supplier) {
                return view('partials.actions', [
                    'id' => $supplier->id,
                    'entity' => 'suppliers',
                ]);
            })
            ->make(true);
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;

        $supplier = Supplier::create($data);

        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return response()->json($supplier);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json(null, 204);
    }
}
