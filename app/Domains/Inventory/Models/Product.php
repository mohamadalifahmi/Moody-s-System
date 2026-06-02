<?php

namespace App\Domains\Inventory\Models;

use App\Domains\Core\Traits\HasCreatorUpdater;
use App\Domains\Core\Traits\HasTenantScope;
use App\Domains\Invoicing\Models\InvoiceItem;
use App\Domains\Sales\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasTenantScope, HasCreatorUpdater;

    protected $fillable = [
        'category_id', 'name', 'sku', 'barcode', 'purchase_price',
        'sale_price', 'stock_quantity', 'unit', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getPriceAttribute(): float
    {
        return (float) $this->sale_price;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
