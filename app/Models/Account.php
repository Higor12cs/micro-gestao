<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use BelongsToTenant, HasFactory, HasSequentialFieldTrait, HasUlids;

    protected $fillable = [
        'tenant_id',
        'sequential',
        'branch',
        'account',
        'name',
        'type',
        'balance',
        'active',
        'created_by',
    ];

    protected $casts = [
        'balance' => 'float',
        'active' => 'boolean',
    ];
}
