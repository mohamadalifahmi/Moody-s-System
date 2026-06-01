<?php

namespace App\Domains\Debts\Models;

use App\Domains\Core\Traits\HasTenantScope;
use App\Domains\Core\Traits\HasCreatorUpdater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasTenantScope, HasCreatorUpdater, SoftDeletes;

    protected $fillable = [
        'creditor_name',
        'amount',
        'paid_amount',
        'description',
        'due_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->paid_amount);
    }
}
