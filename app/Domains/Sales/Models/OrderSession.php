<?php

namespace App\Domains\Sales\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Core\Traits\HasCreatorUpdater;
use App\Domains\Core\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderSession extends Model
{
    use HasTenantScope, HasCreatorUpdater;

    protected $fillable = [
        'user_id', 'opened_at', 'closed_at', 'status',
        'total_cash', 'total_card', 'total_other', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'total_cash' => 'decimal:2',
            'total_card' => 'decimal:2',
            'total_other' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'session_id');
    }
}
