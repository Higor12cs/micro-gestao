<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'stock_total',
        'stock_on_trial',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updateStock(float $quantity, string $type, array $movementData): void
    {
        $this->stock_total += $quantity;
        $this->save();

        StockMovement::create(array_merge($movementData, [
            'tenant_id' => $this->tenant_id,
            'product_id' => $this->product_id,
            'quantity' => abs($quantity),
            'type' => $type,
        ]));
    }
}
