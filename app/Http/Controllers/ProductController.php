<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->latest()->get();

        $scores = $products->pluck('score')
            ->filter(fn ($score) => $score !== null)
            ->map(fn ($score) => (float) $score);

        $averageScore = $scores->isNotEmpty() ? $scores->avg() : 0;
        $gpa = number_format(1 + ($averageScore / 100) * 4, 2);

        $totalCourses = $products->pluck('category')->filter()->unique()->count();
        $totalAssignments = $products->count();
        $completedCount = $products->where('status', 'completed')->count();
        $pendingCount = $products->where('status', 'pending')->count();
        $overdueCount = $products->where('status', 'overdue')->count();

        $attendance = $totalAssignments > 0
            ? round(($completedCount / $totalAssignments) * 100)
            : 0;

        $lowPerformanceCount = $products
            ->filter(fn ($item) =>
                $item->score !== null && (float) $item->score < 90
            )->count();

        $categoryScores = $products
            ->filter(fn ($item) => $item->score !== null && $item->category)
            ->groupBy('category')
            ->map(fn ($items) =>
                round($items->avg(fn ($item) => (float) $item->score), 2)
            );

        $activityDates = $products->sortBy('created_at')->take(-10)->values();

        $attendanceLabels = $activityDates->map(
            fn ($item) => $item->created_at
                ? $item->created_at->format('m-d')
                : 'N/A'
        );

        $attendanceValues = [];
        $completedSoFar = 0;
        $totalSoFar = 0;

        foreach ($activityDates as $item) {
            $totalSoFar++;

            if ($item->status === 'completed') {
                $completedSoFar++;
            }

            $attendanceValues[] = round(
                ($completedSoFar / $totalSoFar) * 100
            );
        }

        return view('dashboard', compact(
            'products',
            'gpa',
            'totalCourses',
            'totalAssignments',
            'completedCount',
            'pendingCount',
            'overdueCount',
            'attendance',
            'lowPerformanceCount',
            'categoryScores',
            'attendanceLabels',
            'attendanceValues'
        ));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'cn' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'status' => 'required|in:completed,pending,overdue',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Activity added successfully.');
    }

    public function show(Product $product)
    {
        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'cn' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'status' => 'required|in:completed,pending,overdue',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
