<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasSequentialFieldTrait;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToTenant, HasFactory, HasSequentialFieldTrait, HasUlids, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'sequential',
        'first_name',
        'last_name',
        'legal_name',
        'cpf_cnpj',
        'rg',
        'ie',
        'birth_date',
        'email',
        'phone',
        'whatsapp',
        'zip_code',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'created_by',
    ];

    protected $hidden = [
        'tenant_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
