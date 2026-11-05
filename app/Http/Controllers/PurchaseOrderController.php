<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\InventoryProduct;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'creator']);

        if ($search = $request->input('search')) {
            $query->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $purchaseOrders = $query->latest()->paginate(10)->withQueryString();

        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = InventoryProduct::all(); 
        $nextReference = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);

        return view('purchase_orders.create', compact('suppliers', 'products', 'nextReference'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_product_id' => 'required|exists:inventory_products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_price'];
            }
            
            $tax = $subtotal * 0.12; // Standard 12% tax calculation
            $totalAmount = $subtotal + $tax;

            $purchaseOrder = PurchaseOrder::create([
                'reference_number' => $request->input('reference_number', 'PO-' . strtoupper(uniqid())),
                'supplier_id' => $validated['supplier_id'],
                'status' => 'draft',
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'inventory_product_id' => $item['inventory_product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_price' => $item['unit_price'],
                    'quantity_received' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully as Draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.inventoryProduct', 'creator', 'approver']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder, $action)
    {
        switch ($action) {
            case 'submit':
                $purchaseOrder->update(['status' => 'pending']);
                break;
            case 'approve':
                $purchaseOrder->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id() ?? 1,
                    'approved_at' => now(),
                ]);
                break;
            case 'reject':
                $purchaseOrder->update(['status' => 'rejected']);
                break;
            case 'send':
                $purchaseOrder->update(['status' => 'sent']);
                break;
            case 'receive':
                DB::transaction(function () use ($purchaseOrder) {
                    foreach ($purchaseOrder->items as $item) {
                        $remainingToReceive = $item->quantity_ordered - $item->quantity_received;
                        if ($remainingToReceive > 0) {
                            $item->increment('quantity_received', $remainingToReceive);
                            
                            $inventoryProduct = $item->inventoryProduct;
                            if ($inventoryProduct) {
                                $inventoryProduct->increment('quantity', $remainingToReceive);
                            }
                        }
                    }
                    $purchaseOrder->update(['status' => 'received']);
                });
                break;
            case 'cancel':
                $purchaseOrder->update(['status' => 'cancelled']);
                break;
        }

        return back()->with('success', "Purchase order status updated to: {$action} successfully.");
    }

    // NEW: Dashboard API Methods for Part 6
    public function apiPendingOrders()
    {
        $orders = PurchaseOrder::with('supplier')->where('status', 'pending')->take(5)->get();
        return response()->json($orders);
    }

    public function apiRecentOrders()
    {
        $orders = PurchaseOrder::with('supplier')->latest()->take(5)->get();
        return response()->json($orders);
    }
}