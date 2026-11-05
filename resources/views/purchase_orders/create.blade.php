@extends('layouts.app') {{-- Matches your existing Student Portal layout --}}

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Create Purchase Order</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i> New Purchase Order Form
        </div>
        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" value="{{ $nextReference }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }} (Lead Time: {{ $supplier->lead_time_days }} days)
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                        @error('order_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="expected_delivery_date" class="form-label">Expected Delivery</label>
                        <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                        @error('expected_delivery_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes & Terms</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>

                <hr>
                <h4>Order Line Items</h4>
                <p class="text-muted small">Select inventory items, enter quantities, and unit pricing. Use the button below to add multiple products.</p>

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th width="45%">Product</th>
                                <th width="20%">Quantity</th>
                                <th width="20%">Unit Price ($)</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][inventory_product_id]" class="form-select product-select" required>
                                        <option value="">Select Inventory Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->unit_price ?? 0 }}">
                                                {{ $product->name }} (SKU: {{ $product->sku }} | Stock: {{ $product->quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity_ordered]" class="form-control qty-input" value="1" min="1" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][unit_price]" class="form-control price-input" value="0.00" min="0" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addRowBtn">
                    <i class="fas fa-plus"></i> Add Another Product Line
                </button>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Purchase Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIndex = 1;

        // Automatically populate unit price when product is selected
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('product-select')) {
                let selectedOption = e.target.options[e.target.selectedIndex];
                let price = selectedOption.getAttribute('data-price') || 0;
                let row = e.target.closest('tr');
                row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
            }
        });

        // Add dynamic row
        document.getElementById('addRowBtn').addEventListener('click', function () {
            let tableBody = document.querySelector('#itemsTable tbody');
            let firstRow = tableBody.querySelector('tr');
            let newRow = firstRow.cloneNode(true);

            // Update names for array indexing
            newRow.querySelectorAll('select, input').forEach(function(element) {
                let name = element.getAttribute('name');
                if (name) {
                    element.setAttribute('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
                    if (element.tagName === 'INPUT') element.value = (element.classList.contains('qty-input') ? '1' : '0.00');
                    if (element.tagName === 'SELECT') element.value = '';
                }
            });

            // Enable delete button on cloned row
            let removeBtn = newRow.querySelector('.remove-row');
            removeBtn.removeAttribute('disabled');

            tableBody.appendChild(newRow);
            rowIndex++;
        });

        // Remove row event
        document.addEventListener('click', function (e) {
            if (e.target && (e.target.classList.contains('remove-row') || e.target.closest('.remove-row'))) {
                let row = e.target.closest('tr');
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                }
            }
        });
    });
</script>
@endpush
@endsection