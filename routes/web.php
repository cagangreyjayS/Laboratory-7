<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\SupplierController;            
use App\Http\Controllers\PurchaseOrderController;       
use App\Http\Controllers\StudentActivityController;     

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Existing product CRUD routes
    Route::resource('products', ProductController::class);

    // NEW: Inventory Transactions resource routes for Laboratory 6
    Route::resource('transactions', InventoryTransactionController::class);

    // NEW: Stock Adjustment Routes for specific products (Part 8)
    Route::get('/inventory/{product}/adjust', [InventoryTransactionController::class, 'createAdjustment'])
        ->name('inventory.adjust');
    Route::post('/inventory/{product}/adjust', [InventoryTransactionController::class, 'storeAdjustment'])
        ->name('inventory.storeAdjustment');

    // Inventory page (from Lab 5)
    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');

    // NEW: Inventory Products CSV Export Route (Part 8)
    Route::get('/inventory/export-csv', [InventoryController::class, 'exportProductsCsv'])
        ->name('inventory.export');

    // Add inventory product
    Route::post('/inventory/products', [InventoryController::class, 'storeProduct'])
        ->name('inventory.products.store');

    // Edit inventory product
    Route::get('/inventory/products/{product}/edit', [InventoryController::class, 'editProduct'])
        ->name('inventory.products.edit');

    // Update inventory product
    Route::put('/inventory/products/{product}', [InventoryController::class, 'updateProduct'])
        ->name('inventory.products.update');

    // Delete inventory product
    Route::delete('/inventory/products/{product}', [InventoryController::class, 'destroyProduct'])
        ->name('inventory.products.destroy');

    // Stock transactions
    Route::post('/inventory/transactions', [InventoryController::class, 'storeTransaction'])
        ->name('inventory.transactions.store');

    // Inventory API endpoints
    Route::get('/inventory/stats', [InventoryController::class, 'stats'])
        ->name('inventory.stats');

    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])
        ->name('inventory.low-stock');

    Route::get('/inventory/recent-transactions', [InventoryController::class, 'recentTransactions'])
        ->name('inventory.recent-transactions');


    // ==========================================
    // NEW: LABORATORY 7 MODULE ROUTES
    // ==========================================

    // 1. Supplier Management Routes (Part 1)
    Route::resource('suppliers', SupplierController::class);

    // 2. Purchase Order Management Routes (Part 2 & 3)
    Route::resource('purchase-orders', PurchaseOrderController::class);
    
    // Purchase Order Status Update Workflow (Submit, Approve, Reject, Send, Receive, Cancel)
    Route::post('purchase-orders/{purchaseOrder}/status/{action}', [PurchaseOrderController::class, 'updateStatus'])
        ->name('purchase-orders.status');

    // 3. Procurement Dashboard & Widget API Endpoints (Part 6)
    Route::get('/api/dashboard/pending-orders', function() {
        return response()->json(['count' => \App\Models\PurchaseOrder::where('status', 'pending')->count()]);
    })->name('api.dashboard.pending-orders');

    Route::get('/api/dashboard/recent-orders', function() {
        return response()->json(\App\Models\PurchaseOrder::with('supplier')->latest()->take(5)->get());
    })->name('api.dashboard.recent-orders');

    Route::get('/api/dashboard/supplier-performance', function() {
        return response()->json(['status' => 'active']);
    })->name('api.dashboard.supplier-performance');

    Route::get('/api/dashboard/reorder-alerts', function() {
        return response()->json(['count' => \App\Models\InventoryProduct::whereRaw('quantity <= reorder_level')->count()]);
    })->name('api.dashboard.reorder-alerts');

    // 4. Supplier Performance Report Page & Individual Performance (Part 7)
    Route::get('/suppliers-performance-report', [SupplierController::class, 'performanceReport'])
        ->name('suppliers.performance-report');

    // NEW: Supplier Performance CSV Export Route
    Route::get('/suppliers-performance-report/export', [SupplierController::class, 'exportCsv'])
        ->name('suppliers.export-csv');
        
    Route::get('suppliers/{supplier}/performance', [SupplierController::class, 'performance'])
        ->name('suppliers.performance');

    // 5. Student Activities Export Route
    Route::get('/student-activities/export', [StudentActivityController::class, 'exportCsv'])
        ->name('student-activities.export');

});

require __DIR__.'/auth.php';