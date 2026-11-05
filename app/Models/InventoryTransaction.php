<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    public function inventoryProduct(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStockIn(Builder $query): Builder
    {
        return $query->where('type', 'stock_in');
    }

    public function scopeStockOut(Builder $query): Builder
    {
        return $query->where('type', 'stock_out');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->latest();
    }
}