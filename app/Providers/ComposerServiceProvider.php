<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PurchaseOrder;
use App\Models\InventoryProduct;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share global pending orders and low stock counts safely across all views using the view() helper
        view()->composer('*', function ($view) {
            // Check if user is authenticated and database tables exist to prevent boot crashes
            if (!app()->runningInConsole() && auth()->check()) {
                $pendingOrdersCount = PurchaseOrder::where('status', 'pending')->count();
                $lowStockCount = InventoryProduct::lowStock()->count();

                $view->with('globalPendingOrdersCount', $pendingOrdersCount)
                     ->with('globalLowStockCount', $lowStockCount);
            }
        });
    }
}