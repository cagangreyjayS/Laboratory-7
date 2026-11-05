<?php

namespace App\Services;

use App\Models\InventoryProduct;
use App\Models\PurchaseOrder;
use App\Models\InventoryTransaction;

class SearchService
{
    /**
     * Advanced search and filter for inventory products.
     */
    public function searchProducts(array $filters)
    {
        $query = InventoryProduct::with('supplier');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'low_stock') {
                $query->whereColumn('quantity', '<=', 'reorder_level');
            } elseif ($filters['status'] === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            }
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Advanced search and filter for purchase orders.
     */
    public function searchPurchaseOrders(array $filters)
    {
        $query = PurchaseOrder::with(['supplier', 'createdBy']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('reference_number', 'like', "%{$search}%");
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Advanced search and filter for inventory transactions.
     */
    public function searchTransactions(array $filters)
    {
        $query = InventoryTransaction::with(['inventoryProduct', 'user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('inventoryProduct', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(10);
    }
}