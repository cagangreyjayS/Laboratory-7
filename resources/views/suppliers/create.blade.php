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
                <h2>Add New Supplier</h2>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to Suppliers</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="physical_address" class="form-label">Physical Address</label>
                                <textarea class="form-control" id="physical_address" name="physical_address" rows="3" required>{{ old('physical_address') }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <label for="payment_terms" class="form-label">Payment Terms</label>
                                <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms') }}" placeholder="e.g. Net 30" required>
                            </div>

                            <div class="col-md-4">
                                <label for="lead_time" class="form-label">Lead Time (Days)</label>
                                <input type="number" class="form-control" id="lead_time" name="lead_time" value="{{ old('lead_time', 5) }}" min="0" required>
                            </div>

                            <div class="col-md-4">
                                <label for="minimum_order_amount" class="form-label">Minimum Order Amount ($)</label>
                                <input type="number" step="0.01" class="form-control" id="minimum_order_amount" name="minimum_order_amount" value="{{ old('minimum_order_amount', 100.00) }}" min="0" required>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Save Supplier</button>
                                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection