<?php

namespace App\Domains\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTenantScope
{
    public static function bootHasTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = config('current_tenant_id');

            if (!$tenantId) {
                $tenantId = session('tenant_id', config('app.tenant_id'));
            }

            if ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            }
        });

        static::creating(function (Model $model) {
            $tenantId = config('current_tenant_id');

            if (!$tenantId) {
                $tenantId = session('tenant_id', config('app.tenant_id'));
            }

            if ($tenantId && !$model->tenant_id) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(config('domains.models.tenant', \App\Models\Tenant::class));
    }
}
