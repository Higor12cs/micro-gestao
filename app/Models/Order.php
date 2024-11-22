<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUlids, HasSequentialFieldTrait;

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
        'total_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'freight' => 'decimal:2',
        'total_price' => 'decimal:2',
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

    public function hasReceivables()
    {
        return $this->relationLoaded('receivables')
            ? $this->receivables->isNotEmpty()
            : $this->receivables()->exists();
    }
}
