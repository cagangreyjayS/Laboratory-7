<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    public function exportCsv()
    {
        $filename = 'student-activities-' . date('Y-m-d') . '.csv';
        
        $activities = InventoryProduct::all(); 

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, ['Date', 'Category', 'Activity', 'Status', 'Score']);

            // CSV Data Rows
            foreach ($activities as $product) {
                fputcsv($file, [
                    $product->created_at ? $product->created_at->format('Y-m-d') : 'N/A',
                    $product->category ?? 'General',
                    $product->name,
                    ucfirst($product->status ?? 'pending'),
                    $product->score ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}