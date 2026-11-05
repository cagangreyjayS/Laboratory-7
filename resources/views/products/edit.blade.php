@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card stat-card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-primary-custom py-3">
                <h5 class="mb-0 fw-bold text-white">Edit Activity / Item</h5>
            </div>
            <div class="card-body p-4 bg-white">
                
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.update', $product->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <!-- Subject -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="subject" class="form-label fw-semibold">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject', $product->subject) }}" required>
                        </div>

                        <!-- CN (Course / Control Number) -->
                        <div class="col-md-6">
                            <label for="cn" class="form-label fw-semibold">CN (Course Number)</label>
                            <input type="text" class="form-control" id="cn" name="cn" value="{{ old('cn', $product->cn) }}" required>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label fw-semibold">Category</label>
                        <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $product->category) }}">
                    </div>

                    <!-- Name / Activity Title -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name / Activity Title</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="row mb-4">
                        <!-- Status -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ old('status', $product->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $product->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <!-- Score (Optional) -->
                        <div class="col-md-6">
                            <label for="score" class="form-label fw-semibold">Score (Optional)</label>
                            <input type="number" class="form-control" id="score" name="score" value="{{ old('score', $product->score) }}">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4" style="background-color: #198754;">Update Record</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection