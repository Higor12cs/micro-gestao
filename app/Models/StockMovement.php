<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use BelongsToTenant, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'order_item_id',
        'trial_item_id',
        'purchase_item_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'unit_price',
        'total_price',
        'type',
        'observation',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_cost' => 'float',
        'total_cost' => 'float',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    // public function trialItem()
    // {
    //     return $this->belongsTo(TrialItem::class);
    // }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
