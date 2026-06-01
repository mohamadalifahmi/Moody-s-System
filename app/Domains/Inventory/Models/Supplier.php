<?php

namespace App\Domains\Inventory\Models;

use App\Domains\Core\Traits\HasCreatorUpdater;
use App\Domains\Core\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasTenantScope, HasCreatorUpdater;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'is_active',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
