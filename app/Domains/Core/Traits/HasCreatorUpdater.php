<?php

namespace App\Domains\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreatorUpdater
{
    public static function bootHasCreatorUpdater(): void
    {
        static::creating(function (Model $model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function (Model $model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('domains.models.user', \App\Models\User::class), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('domains.models.user', \App\Models\User::class), 'updated_by');
    }
}
