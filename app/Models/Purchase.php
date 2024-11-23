<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, HasSequentialFieldTrait, HasUlids, SoftDeletes;

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
        'total' => 'float',
        'discount' => 'float',
        'freight' => 'float',
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
        return $this->hasMany(PurchaseItem::class);
    }

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class, 'purchase_id');
    }

    public function hasPayables(): bool
    {
        return $this->relationLoaded('payables')
            ? $this->payables->isNotEmpty()
            : $this->payables()->exists();
    }
}
