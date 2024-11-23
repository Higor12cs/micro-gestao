<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'subscription_ends_at' => 'date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
