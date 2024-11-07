<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasSequentialFieldTrait
{
    protected static function bootHasSequentialFieldTrait(): void
    {
        static::creating(function (Model $model) {
            if (static::hasSequentialField()) {
                $model->sequential = static::getNextSequentialNumber(auth()->user()->tenant->id ?? $model->tenant_id);
            }
        });
    }

    protected static function getNextSequentialNumber(string $tenantId): int
    {
        $maxSequentialNumber = static::where('tenant_id', $tenantId)->max('sequential');

        return $maxSequentialNumber ? $maxSequentialNumber + 1 : 1;
    }

    protected static function hasSequentialField(): bool
    {
        return in_array('sequential', (new static)->getFillable());
    }
}
