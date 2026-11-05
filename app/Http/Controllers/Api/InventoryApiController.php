<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    /**
     * Return overall dashboard inventory statistics.
     */
    public function stats()
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $totalTransactions = InventoryTransaction::count();

        return response()->json([
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_transactions' => $totalTransactions,
            'last_updated' => now()->format('M d, Y h:i:s A'),
        ]);
    }

    /**
     * Return JSON list of low stock products.
     */
    public function lowStock()
    {
        $products = Product::lowStock()->get();

        return response()->json([
            'count' => $products->count(),
            'products' => $products,
        ]);
    }

    /**
     * Return JSON list of recent inventory transactions.
     */
    public function recentTransactions()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json($transactions);
    }
}