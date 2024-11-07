<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Group;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $products = Product::query()
            ->with('stocks')
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get();

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name,
            ];
        });

        return response()->json($products);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query()
                ->with('stocks')
                ->where('tenant_id', auth()->user()->tenant->id)
                ->select('products.*');

            return DataTables::make($query)
                ->editColumn('sequential', function ($product) {
                    return str_pad($product->sequential, 5, '0', STR_PAD_LEFT);
                })
                ->editColumn('active', function ($product) {
                    return view('partials.active', [
                        'active' => $product->active,
                    ]);
                })
                ->addColumn('actions', function ($product) {
                    return view('partials.actions', [
                        'id' => $product->id,
                        'entity' => 'products',
                    ]);
                })
                ->make(true);
        }

        $sections = Section::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->get();

        $groups = Group::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->get();

        $brands = Brand::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->get();

        return view('products.index', compact('sections', 'groups', 'brands'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $product->update($data);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
