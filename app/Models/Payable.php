<?php

namespace App\Models;

use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payable extends Model
{
    use HasFactory, HasSequentialFieldTrait, HasUlids, SoftDeletes;

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
        'amount' => 'float',
        'paid_amount' => 'float',
        'paid_at' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
