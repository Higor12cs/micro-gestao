<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use BelongsToTenant, HasFactory, HasSequentialFieldTrait, HasUlids;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'sequential',
        'date',
        'total_cost',
        'discount',
        'freight',
        'total_price',
        'observation',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_cost' => 'float',
        'discount' => 'float',
        'freight' => 'float',
        'total_price' => 'float',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function hasReceivables(): bool
    {
        return $this->relationLoaded('receivables')
            ? $this->receivables->isNotEmpty()
            : $this->receivables()->exists();
    }

    public function calculateTotalPrice(): float
    {
        return $this->total_cost + $this->freight - $this->discount;
    }
}
