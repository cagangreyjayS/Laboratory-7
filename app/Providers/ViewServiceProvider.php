<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\PurchaseOrder;
use App\Models\InventoryProduct;

class ViewServiceProvider extends ServiceProvider
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
        // Bind data to dashboard widgets (adjust 'dashboard' to match your main view name if different)
        View::composer('dashboard', function ($view) {
            $view->with('pendingOrdersCount', PurchaseOrder::where('status', 'pending')->count());
            $view->with('recentOrders', PurchaseOrder::with('supplier')->latest()->take(5)->get());
            $view->with('reorderAlertsCount', InventoryProduct::whereRaw('quantity <= reorder_level')->count());
        });
    }
}