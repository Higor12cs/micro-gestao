<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory, HasSequentialFieldTrait, HasUuids;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'sequential',
        'date',
        'total',
        'discount',
        'freight',
        'observation',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'freight' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'purchase_id')
            ->where('tenant_id', $this->tenant_id);
    }

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class, 'purchase_id')
            ->where('tenant_id', $this->tenant_id);
    }

    public function hasPayables(): bool
    {
        return $this->payables()->exists();
    }
}
