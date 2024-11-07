<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, HasUuids;

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
