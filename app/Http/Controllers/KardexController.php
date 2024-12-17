<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KardexController extends Controller
{
    public function index(?Product $product = null)
    {
        return view('kardex.index', [
            'product' => $product,
        ]);
    }

    public function redirectToProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        return redirect()->route('kardex.index', ['product' => $product->sequential]);
    }

    public function getMovements(Product $product)
    {
        $movements = StockMovement::where('product_id', $product->id)
            ->with([
                'orderItem.order' => fn ($query) => $query->withTrashed(),
                'purchaseItem.purchase' => fn ($query) => $query->withTrashed(),
            ]);

        return DataTables::of($movements)
            ->addColumn('created_at', function ($movement) {
                return [
                    'display' => e($movement->created_at->format('d/m/Y - H:i')),
                    'timestamp' => $movement->created_at->timestamp,
                ];
            })
            ->addColumn('type', fn ($movement) => strtoupper($movement->type))
            ->addColumn('quantity', fn ($movement) => $movement->quantity)
            ->addColumn('unit_cost', fn ($movement) => 'R$ '.number_format($movement->unit_cost, 2, ',', '.'))
            ->addColumn('total_cost', fn ($movement) => 'R$ '.number_format($movement->total_cost, 2, ',', '.'))
            ->addColumn('related_document', function ($movement) {
                if ($movement->orderItem) {
                    return 'Pedido #'.$movement->orderItem->order->sequential;
                }
                if ($movement->purchaseItem) {
                    return 'Compra #'.$movement->purchaseItem->purchase->sequential;
                }

                return '-';
            })
            ->make(true);
    }
}
