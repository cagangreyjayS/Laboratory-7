<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\InventoryProduct;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone_number' => 'required|string|max:50',
            'physical_address' => 'required|string',
            'payment_terms' => 'required|string',
            'lead_time' => 'required|integer|min:0',
            'minimum_order_amount' => 'required|numeric|min:0',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function performanceReport() 
    { 
        $suppliers = Supplier::with('purchaseOrders')->get(); 
        
        $performanceData = $suppliers->map(function ($supplier) { 
            $orders = $supplier->purchaseOrders; 
            $totalOrders = $orders->count(); 
            $completedOrders = $orders->where('status', 'received'); 
            
            $onTimeDeliveries = $completedOrders->filter(function ($order) { 
                return $order->expected_delivery_date && $order->updated_at <= $order->expected_delivery_date; 
            })->count(); 

            return [ 
                'supplier' => $supplier, 
                'total_orders' => $totalOrders, 
                'completed_orders' => $completedOrders->count(), 
                'on_time_rate' => $totalOrders > 0 ? ($onTimeDeliveries / $totalOrders) * 100 : 0, 
                'total_value' => $orders->sum('total_amount'), 
            ]; 
        }); 

        return view('suppliers.performance-report', compact('performanceData')); 
    }

    public function exportCsv()
    {
        $filename = 'supplier-performance-report-' . date('Y-m-d') . '.csv';
        $suppliers = Supplier::with('purchaseOrders')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($suppliers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Company Name', 'Total Orders', 'Completed Orders', 'On-Time Rate (%)', 'Total Order Value']);

            foreach ($suppliers as $supplier) {
                $orders = $supplier->purchaseOrders;
                $totalOrders = $orders->count();
                $completedOrders = $orders->where('status', 'received')->count();
                $onTimeDeliveries = $orders->where('status', 'received')->filter(function ($order) {
                    return $order->expected_delivery_date && $order->updated_at <= $order->expected_delivery_date;
                })->count();
                $onTimeRate = $totalOrders > 0 ? ($onTimeDeliveries / $totalOrders) * 100 : 0;
                $totalValue = $orders->sum('total_amount');

                fputcsv($file, [
                    $supplier->company_name,
                    $totalOrders,
                    $completedOrders,
                    number_format($onTimeRate, 1) . '%',
                    $totalValue
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}