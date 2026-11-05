<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'lead_time_days',
        'status', // Added if status column exists
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
    ];

    // Relationships
    public function inventoryProducts()
    {
        return $this->hasMany(InventoryProduct::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Query Scopes
    public function scopeActive($query)
{
    if (Schema::hasColumn('suppliers', 'status')) {
        return $query->where('status', 'active');
    }
    
    return $query; // Falls back to returning all suppliers if 'status' column is absent
}

    public function scopeMaxLeadTime($query, $days)
    {
        return $query->where('lead_time_days', '<=', $days);
    }

    // Accessors
    public function getFullContactAttribute()
    {
        return "{$this->contact_person} ({$this->email}) - {$this->phone}";
    }
}