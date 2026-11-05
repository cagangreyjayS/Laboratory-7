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
                <h2>Purchase Order Details: <strong>{{ $purchaseOrder->reference_number }}</strong></h2>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">← Back to List</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Order Information</h5>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-secondary text-uppercase">{{ $purchaseOrder->status }}</span>
                            </p>
                            <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date }}</p>
                            <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ?? 'N/A' }}</p>
                            <p><strong>Created By:</strong> {{ $purchaseOrder->creator->name ?? 'System' }}</p>
                            <p><strong>Notes:</strong> {{ $purchaseOrder->notes ?? 'None' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Supplier Information</h5>
                            <p><strong>Company:</strong> {{ $purchaseOrder->supplier->company_name ?? 'N/A' }}</p>
                            <p><strong>Contact Person:</strong> {{ $purchaseOrder->supplier->contact_person ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $purchaseOrder->supplier->email ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $purchaseOrder->supplier->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ITEMS TABLE --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Ordered Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Quantity Ordered</th>
                                    <th>Quantity Received</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                    <tr>
                                        <td>{{ $item->inventoryProduct->name ?? 'Unknown Product' }}</td>
                                        <td>{{ $item->inventoryProduct->sku ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity_ordered }}</td>
                                        <td>{{ $item->quantity_received }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->quantity_ordered * $item->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end text-end mt-3">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Subtotal:</strong> ${{ number_format($purchaseOrder->subtotal, 2) }}</p>
                            <p class="mb-1"><strong>Tax (12%):</strong> ${{ number_format($purchaseOrder->tax, 2) }}</p>
                            <h4 class="text-success"><strong>Total Amount: ${{ number_format($purchaseOrder->total_amount, 2) }}</strong></h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WORKFLOW ACTION BUTTONS --}}
            <div class="card shadow-sm">
                <div class="card-body d-flex gap-2 flex-wrap">
                    <h5 class="w-100 text-muted mb-2">Workflow Actions</h5>
                    
                    @if($purchaseOrder->status === 'draft')
                        <form action="{{ route('purchase-orders.status', [$purchaseOrder->id, 'submit']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning">Submit for Approval</button>
                        </form>
                    @endif

                    @if($purchaseOrder->status === 'pending')
                        <form action="{{ route('purchase-orders.status', [$purchaseOrder->id, 'approve']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Approve Order</button>
                        </form>
                        <form action="{{ route('purchase-orders.status', [$purchaseOrder->id, 'reject']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Reject Order</button>
                        </form>
                    @endif

                    @if($purchaseOrder->status === 'approved')
                        <form action="{{ route('purchase-orders.status', [$purchaseOrder->id, 'send']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info text-white">Mark as Sent</button>
                        </form>
                    @endif

                    @if(in_array($purchaseOrder->status, ['approved', 'sent']))
                        <form action="{{ route('purchase-orders.status', [$purchaseOrder->id, 'receive']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Receive Stock (Restock)</button>
                        </form>
                    @endif

                    @if(!in_array($purchaseOrder->status, ['received', 'cancelled']))
                        <form action="{{ route('purchase-orders.status', [$purchaseOrder->id, 'cancel']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Cancel Order</button>
                        </form>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection