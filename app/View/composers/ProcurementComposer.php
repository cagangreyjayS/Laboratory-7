<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\PurchaseOrder;
use App\Models\InventoryProduct;

class ProcurementComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (!app()->runningInConsole() && auth()->check()) {
            try {
                $pendingOrdersCount = PurchaseOrder::where('status', 'pending')->count();
                $lowStockCount = InventoryProduct::lowStock()->count();

                $view->with('globalPendingOrdersCount', $pendingOrdersCount)
                     ->with('globalLowStockCount', $lowStockCount);
            } catch (\Exception $e) {
                // Safely catch exceptions if tables are not yet migrated
            }
        }
    }
}