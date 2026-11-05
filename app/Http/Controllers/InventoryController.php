<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index(Request $request, SearchService $searchService)
    {
        // Use SearchService to handle search and filter query parameters
        $products = $searchService->searchProducts($request->all());

        $lowStockProducts = InventoryProduct::lowStock()->get();

        $transactions = InventoryTransaction::with([
            'inventoryProduct',
            'user',
        ])->recent()->take(10)->get();

        return view('inventory.index', compact(
            'products',
            'lowStockProducts',
            'transactions'
        ));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:inventory_products,sku',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        InventoryProduct::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory product added successfully.');
    }

    public function editProduct(InventoryProduct $product)
    {
        // Updated to fetch all suppliers so the dropdown populates correctly
        $suppliers = \App\Models\Supplier::all();
        return view('inventory.edit', compact('product', 'suppliers'));
    }

    public function updateProduct(Request $request, InventoryProduct $product)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inventory_products', 'sku')->ignore($product->id),
            ],
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory product updated successfully.');
    }

    public function destroyProduct(InventoryProduct $product)
    {
        // Delete related inventory transactions first to prevent foreign key errors
        $product->transactions()->delete();

        // Safely delete the product without risking column mismatches
        $product->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory product deleted successfully.');
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'inventory_product_id' => 'required|exists:inventory_products,id',
            'type' => 'required|in:stock_in,stock_out',
            'quantity' => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            $product = InventoryProduct::whereKey(
                $validated['inventory_product_id']
            )->lockForUpdate()->firstOrFail();

            if (
                $validated['type'] === 'stock_out' &&
                $product->quantity < $validated['quantity']
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'Not enough stock available.',
                ]);
            }

            InventoryTransaction::create($validated);
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory transaction recorded.');
    }

    public function stats()
    {
        return response()->json([
            'total_products' => InventoryProduct::count(),
            'total_stock' => InventoryProduct::sum('quantity'),
            'low_stock_count' => InventoryProduct::lowStock()->count(),
            'recent_transactions' => InventoryTransaction::count(),
        ]);
    }

    public function lowStock()
    {
        return response()->json(InventoryProduct::lowStock()->get());
    }

    public function recentTransactions()
    {
        return response()->json(
            InventoryTransaction::with('inventoryProduct')
                ->recent()
                ->take(10)
                ->get()
        );
    }

    public function apiReorderAlerts()
    {
        $lowStockProducts = InventoryProduct::with('supplier')->lowStock()->take(5)->get();
        return response()->json($lowStockProducts);
    }

    // CSV Export Method for Inventory Products (Part 8)
    public function exportProductsCsv()
    {
        $filename = 'inventory-products-' . date('Y-m-d') . '.csv';
        $products = InventoryProduct::with('supplier')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, ['ID', 'Name', 'SKU', 'Category', 'Quantity', 'Reorder Level', 'Unit Price', 'Supplier']);

            // CSV Data Rows
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category ?? 'General',
                    $product->quantity,
                    $product->reorder_level,
                    $product->unit_price,
                    $product->supplier ? $product->supplier->name : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}