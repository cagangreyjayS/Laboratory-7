<?php

namespace App\Http\Controllers;

use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        // Get existing records.
        $products = Product::all();

        // Calculate average score and GPA.
        $scores = $products
            ->pluck('score')
            ->filter(fn ($score) => $score !== null)
            ->map(fn ($score) => (float) $score);

        $averageScore = $scores->isNotEmpty()
            ? $scores->avg()
            : 0;

        $gpa = number_format(
            1 + ($averageScore / 100) * 4,
            2
        );

        // Calculate dashboard statistics.
        $totalCourses = $products
            ->pluck('category')
            ->filter()
            ->unique()
            ->count();

        $totalAssignments = $products->count();

        $completedCount = $products
            ->where('status', 'completed')
            ->count();

        $pendingCount = $products
            ->where('status', 'pending')
            ->count();

        $overdueCount = $products
            ->where('status', 'overdue')
            ->count();

        $attendance = $totalAssignments > 0
            ? round(
                ($completedCount / $totalAssignments) * 100
            )
            : 0;

        // Count items with scores below 90.
        $lowPerformanceCount = $products
            ->filter(fn ($item) =>
                $item->score !== null &&
                (float) $item->score < 90
            )
            ->count();

        // Calculate average score for each category.
        $categoryScores = $products
            ->filter(fn ($item) => $item->score !== null)
            ->groupBy('category')
            ->map(function ($items) {
                return round(
                    $items->avg(
                        fn ($item) => (float) $item->score
                    ),
                    2
                );
            });

        // Prepare the last 10 activities for the chart.
        $activityDates = $products
            ->sortBy('created_at')
            ->take(-10)
            ->values();

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

        // Send all required data to the dashboard view.
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
}