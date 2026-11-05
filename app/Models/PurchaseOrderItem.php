<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'inventory_product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * Get the purchase order that owns this item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the inventory product associated with this item.
     */
    public function inventoryProduct()
    {
        return $this->belongsTo(InventoryProduct::class);
    }
}