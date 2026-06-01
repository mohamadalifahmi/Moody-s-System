<?php

namespace App\Domains\Inventory\Models;

use App\Domains\Core\Traits\HasCreatorUpdater;
use App\Domains\Core\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasTenantScope, HasCreatorUpdater;

    protected $fillable = [
        'supplier_id', 'invoice_no', 'total', 'paid', 'due',
        'status', 'date', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid' => 'decimal:2',
            'due' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
