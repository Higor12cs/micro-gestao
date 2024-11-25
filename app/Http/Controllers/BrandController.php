<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search', '');

        $brands = Brand::query()
            ->where('name', 'like', "%{$searchTerm}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json(
            $brands->map(fn ($brand) => ['id' => $brand->id, 'text' => $brand->name])
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Brand::query();

            return DataTables::of($query)
                ->editColumn('sequential', fn ($brand) => str_pad($brand->sequential, 5, '0', STR_PAD_LEFT))
                ->editColumn('active', fn ($brand) => view('partials.active', ['active' => $brand->active]))
                ->addColumn('actions', fn ($brand) => view('partials.actions', [
                    'id' => $brand->id,
                    'sequential' => $brand->sequential,
                    'entity' => 'brands',
                ]))
                ->make(true);
        }

        return view('brands.index');
    }

    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $brand = Brand::create($data);

        return response()->json($brand, 201);
    }

    public function show(Brand $brand)
    {
        $this->authorizeTenantAccess($brand);

        return response()->json($brand);
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $this->authorizeTenantAccess($brand);

        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $brand->update($data);

        return response()->json($brand);
    }

    public function destroy(Brand $brand)
    {
        $this->authorizeTenantAccess($brand);

        $brand->delete();

        return response()->json(null, 204);
    }
}
