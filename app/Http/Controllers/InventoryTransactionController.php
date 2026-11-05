<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    /**
     * Display a listing of all inventory transactions.
     */
    public function index()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])->latest()->paginate(10);
        return view('inventory.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $products = Product::all();
        return view('inventory.create', compact('products'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Prevent taking out more stock than currently available
        if ($request->type === 'stock_out' && $product->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Insufficient stock available for removal.'])->withInput();
        }

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory transaction recorded successfully.');
    }

    /**
     * Display the specified transaction details.
     */
    public function show(InventoryTransaction $inventoryTransaction)
    {
        return view('inventory.show', compact('inventoryTransaction'));
    }

    /**
     * Show the form for adjusting a specific product's stock (Part 8).
     */
    public function createAdjustment(Product $product)
    {
        return view('inventory.adjust', compact('product'));
    }

    /**
     * Store a newly created stock adjustment in storage (Part 8).
     */
    public function storeAdjustment(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $quantity = $request->quantity;

        // Prevent negative stock levels on stock_out
        if ($request->type === 'stock_out' && $product->quantity < $quantity) {
            return back()->withErrors(['quantity' => 'Insufficient stock available for removal.'])->withInput();
        }

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'quantity' => $quantity,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Stock adjusted successfully!');
    }
}