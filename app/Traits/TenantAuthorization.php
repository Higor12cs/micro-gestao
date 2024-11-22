<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait TenantAuthorization
{
    public function authorizeTenantAccess(Model $model)
    {
        if ($model->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }
    }
}
