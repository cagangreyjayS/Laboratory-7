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
                <a href="{{ route('suppliers.index') }}" class="nav-link active">🏭 Suppliers</a>
                <a href="{{ route('purchase-orders.index') }}" class="nav-link">📋 Purchase Orders</a>
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
                <h2>Supplier Management</h2>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Add New Supplier</a>
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
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Payment Terms</th>
                                    <th>Lead Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                    <tr>
                                        <td><strong>{{ $supplier->company_name }}</strong></td>
                                        <td>{{ $supplier->contact_person }}</td>
                                        <td>{{ $supplier->email }}</td>
                                        <td>{{ $supplier->phone_number }}</td>
                                        <td>{{ $supplier->payment_terms }}</td>
                                        <td>{{ $supplier->lead_time }} days</td>
                                        <td>
                                            <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($supplier->status ?? 'Active') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-sm btn-info text-white">View</a>
                                            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No suppliers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection