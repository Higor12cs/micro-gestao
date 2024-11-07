<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasSequentialFieldTrait, HasUuids;

    protected $fillable = [
        'tenant_id',
        'sequential',
        'name',
        'barcode',
        'section_id',
        'group_id',
        'brand_id',
        'cost_price',
        'sale_price',
        'minimum_stock',
        'active',
        'created_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (Product $product) {
            $product->stocks()->create([
                'tenant_id' => $product->tenant_id,
                'stock_total' => 0,
            ]);
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
