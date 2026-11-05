<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Inventory Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #DAB49D; color: #5E3023; }
        .card { border: 0; border-radius: 12px; }
        .btn-primary { background: #800776; border-color: #800776; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 700px;">
        <div class="card-body p-4">
            <h2 class="mb-4">Edit Inventory Product</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inventory.products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" for="name">Product Name</label>
                    <input class="form-control" id="name" name="name"
                           value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="sku">SKU</label>
                    <input class="form-control" id="sku" name="sku"
                           value="{{ old('sku', $product->sku) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="category">Category</label>
                    <input class="form-control" id="category" name="category"
                           value="{{ old('category', $product->category) }}">
                </div>

                <!-- NEW: Supplier Selection Dropdown for Laboratory 7 -->
                <div class="mb-3">
                    <label class="form-label" for="supplier_id">Supplier</label>
                    <select class="form-select" id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity"
                               name="quantity" min="0"
                               value="{{ old('quantity', $product->quantity) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="reorder_level">Reorder Level</label>
                        <input type="number" class="form-control" id="reorder_level"
                               name="reorder_level" min="0"
                               value="{{ old('reorder_level', $product->reorder_level) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="unit_price">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price"
                               name="unit_price" min="0" step="0.01"
                               value="{{ old('unit_price', $product->unit_price) }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>