<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Group;
use App\Models\Product;
use App\Models\Section;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $products = Product::with('stock')
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get()
            ->map(fn ($product) => ['id' => $product->id, 'text' => $product->name]);

        return response()->json($products);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with('stock')
                ->select('products.*');

            return DataTables::of($query)
                ->editColumn('sequential', fn ($product) => str_pad($product->sequential, 5, '0', STR_PAD_LEFT))
                ->addColumn('stock_total', fn ($product) => $product->stock ? $product->stock->stock_total : 0)
                ->editColumn('active', fn ($product) => view('partials.active', ['active' => $product->active]))
                ->addColumn('actions', fn ($product) => view('partials.actions', [
                    'id' => $product->id,
                    'sequential' => $product->sequential,
                    'entity' => 'products',
                ]))
                ->make(true);
        }

        $sections = Section::query()->get();
        $groups = Group::query()->get();
        $brands = Brand::query()->get();

        return view('products.index', compact('sections', 'groups', 'brands'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        $this->authorizeTenantAccess($product);

        return response()->json($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorizeTenantAccess($product);

        $product->update($request->validated());

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->authorizeTenantAccess($product);

        $product->delete();

        return response()->json(null, 204);
    }
}
