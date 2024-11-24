<?php

namespace App\Models;

use App\Events\PurchaseItemCreated;
use App\Events\PurchaseItemDeleted;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'purchase_id',
        'product_id',
        'quantity',
        'previous_stock',
        'previous_cost',
        'unit_cost',
        'total_cost',
        'created_by',
    ];

    protected $dispatchesEvents = [
        'created' => PurchaseItemCreated::class,
        'deleted' => PurchaseItemDeleted::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
