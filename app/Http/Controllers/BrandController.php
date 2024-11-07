<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BrandController extends Controller
{
    public function search(Request $request)
    {
        $brands = Brand::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get();

        $brands = $brands->map(function ($brand) {
            return [
                'id' => $brand->id,
                'text' => $brand->name,
            ];
        });

        return response()->json($brands);
    }

    public function index()
    {
        $query = Brand::query()
            ->where('tenant_id', auth()->user()->tenant->id);

        return DataTables::make($query)
            ->editColumn('sequential', function ($brand) {
                return str_pad($brand->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->editColumn('active', function ($brand) {
                return view('partials.active', [
                    'active' => $brand->active,
                ]);
            })
            ->addColumn('actions', function ($brand) {
                return view('partials.actions', [
                    'id' => $brand->id,
                    'entity' => 'brands',
                ]);
            })
            ->make(true);
    }

    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $brand = Brand::create($data);

        return response()->json($brand, 201);
    }

    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $brand->update($data);

        return response()->json($brand);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->json(null, 204);
    }
}
