<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receivable extends Model
{
    use HasFactory, HasSequentialFieldTrait, HasUlids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'sequential',
        'customer_id',
        'order_id',
        'due_date',
        'amount',
        'paid_amount',
        'paid_at',
        'paid_by',
        'status',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'float',
        'paid_amount' => 'float',
        'paid_at' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
