@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- SIDEBAR --}}
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="mb-4">Navigation</h5>
            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}" class="nav-link">🏠 Dashboard</a>
                <a href="{{ route('inventory.index') }}" class="nav-link">📦 Inventory</a>
                <a href="{{ route('suppliers.index') }}" class="nav-link">🏭 Suppliers</a>
                <a href="{{ route('purchase-orders.index') }}" class="nav-link active">📋 Purchase Orders</a>
                <a href="{{ route('suppliers.performance-report') }}" class="nav-link">📊 Supplier Performance</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="nav-link text-start w-100 border-0 bg-transparent">🚪 Log Out</button>
                </form>
            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Purchase Orders</h2>
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">+ Create Purchase Order</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $order)
                                    <tr>
                                        <td><strong>{{ $order->reference_number }}</strong></td>
                                        <td>{{ $order->supplier->company_name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeColor = match($order->status) {
                                                    'approved' => 'success',
                                                    'pending' => 'warning text-dark',
                                                    'received' => 'info text-white',
                                                    'rejected', 'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->order_date ?? $order->created_at->format('Y-m-d') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <a href="{{ route('purchase-orders.show', $order->id) }}" class="btn btn-sm btn-info text-white">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No purchase orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $purchaseOrders->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection