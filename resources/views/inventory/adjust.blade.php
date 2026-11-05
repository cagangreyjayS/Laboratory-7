<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Adjust Stock: ') }} {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-4 text-sm text-gray-600">
                    Current Stock Level: <span class="font-bold text-gray-900">{{ $product->quantity }}</span> | 
                    Minimum Threshold: <span class="font-bold text-gray-900">{{ $product->min_threshold ?? 5 }}</span>
                </div>

                <!-- Display validation error alerts if any -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('inventory.storeAdjustment', $product->id) }}">
                    @csrf

                    <!-- Adjustment Type -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700">Adjustment Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="stock_in">Stock In (Add Inventory)</option>
                            <option value="stock_out">Stock Out (Remove Inventory)</option>
                            <option value="adjustment">Direct Adjustment</option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>

                    <!-- Notes / Reason -->
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes / Reason</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g. Restocked from supplier, or Damaged items removed">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('inventory.index') }}" class="text-gray-600 underline mr-4">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Adjustment
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>