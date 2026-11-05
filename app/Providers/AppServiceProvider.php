<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\PurchaseOrder;
use App\Models\InventoryProduct;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip view composers entirely when running artisan console commands
        if ($this->app->runningInConsole()) {
            return;
        }

        // Safely share global procurement counts when authenticated
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $pendingOrdersCount = PurchaseOrder::where('status', 'pending')->count();
                $lowStockCount = InventoryProduct::lowStock()->count();

                $view->with('globalPendingOrdersCount', $pendingOrdersCount)
                     ->with('globalLowStockCount', $lowStockCount);
            }
        });
    }
}