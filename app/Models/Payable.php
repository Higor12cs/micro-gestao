<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory, HasSequentialFieldTrait, HasUlids;

    protected $fillable = [
        'tenant_id',
        'sequential',
        'supplier_id',
        'purchase_id',
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
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class)
            ->where('tenant_id', $this->tenant_id);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)
            ->where('tenant_id', $this->tenant_id);
    }
}
