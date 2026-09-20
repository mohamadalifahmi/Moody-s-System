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

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'session_id');
    }

    public function getSessionNumberAttribute(): string
    {
        return 'S-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getOrdersCountAttribute(): int
    {
        return $this->relationLoaded('orders') ? $this->orders->count() : $this->orders()->count();
    }

    public function getTotalCashAttribute(): float
    {
        return (float) $this->orders->flatMap->payments->where('payment_method', 'cash')->sum('amount');
    }

    public function getTotalCardAttribute(): float
    {
        return (float) $this->orders->flatMap->payments->where('payment_method', 'card')->sum('amount');
    }

    public function getTotalOtherAttribute(): float
    {
        return (float) $this->orders->flatMap->payments->where('payment_method', 'other')->sum('amount');
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->total_cash + $this->total_card + $this->total_other;
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->orders->sum('total');
    }
}
