<?php

namespace App\Domains\Auth\Models;

use App\Domains\Core\Traits\HasCreatorUpdater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasCreatorUpdater;

    protected $fillable = [
        'name', 'slug', 'business_type', 'email', 'phone', 'address', 'logo',
        'currency', 'timezone', 'is_active', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
