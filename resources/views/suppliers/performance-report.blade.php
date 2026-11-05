@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="mb-4">Navigation</h5>
            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}" class="nav-link">🏠 Dashboard</a>
                <a href="{{ route('inventory.index') }}" class="nav-link">📦 Inventory</a>
                <a href="{{ route('suppliers.index') }}" class="nav-link">🏭 Suppliers</a>
                <a href="{{ route('purchase-orders.index') }}" class="nav-link">📋 Purchase Orders</a>
                <a href="{{ route('suppliers.performance-report') }}" class="nav-link active">📊 Supplier Performance</a>
            </nav>
        </aside>

        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Supplier Performance Report</h2>
                <a href="{{ route('suppliers.export-csv') }}" class="btn btn-success">
                    📥 Export Performance CSV
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Total Orders</th>
                                    <th>Completed Orders</th>
                                    <th>On-Time Delivery Rate</th>
                                    <th>Total Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($performanceData as $data)
                                    <tr>
                                        <td><strong>{{ $data['supplier']->company_name }}</strong></td>
                                        <td>{{ $data['total_orders'] }}</td>
                                        <td>{{ $data['completed_orders'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $data['on_time_rate'] >= 75 ? 'success' : 'warning' }}">
                                                {{ number_format($data['on_time_rate'], 1) }}%
                                            </span>
                                        </td>
                                        <td>${{ number_format($data['total_value'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No performance data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection