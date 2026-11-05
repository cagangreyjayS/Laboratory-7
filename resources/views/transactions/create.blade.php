<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record Inventory Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('transactions.store') }}">
                    @csrf

                    <!-- Product Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Select Product</label>
                        <select name="product_id" id="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">-- Choose Product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">
                                    {{ $product->name }} (Current Stock: {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                        <small id="stock-display" class="text-gray-500 mt-1 block"></small>
                    </div>

                    <!-- Transaction Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="stock_in">Stock In (Add Inventory)</option>
                            <option value="stock_out">Stock Out (Remove Inventory)</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <!-- Reference Number -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Reference Number (Optional)</label>
                        <input type="text" name="reference_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g. PO-2026-001">
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Transaction
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- JavaScript Enhancement for dynamic stock info -->
    @push('scripts')
    <script>
        document.getElementById('product_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let stock = selectedOption.getAttribute('data-quantity');
            let stockDisplay = document.getElementById('stock-display');
            if (stock) {
                stockDisplay.textContent = "Available system quantity: " + stock;
            } else {
                stockDisplay.textContent = "";
            }
        });
    </script>
    @endpush
</x-app-layout>