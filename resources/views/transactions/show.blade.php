<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details #' . $transaction->id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Transaction Info Card -->
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-3">Transaction Information</h3>
                        <p><strong>Type:</strong> <span class="uppercase font-semibold">{{ $transaction->type }}</span></p>
                        <p><strong>Quantity Changed:</strong> {{ $transaction->quantity }}</p>
                        <p><strong>Reference Number:</strong> {{ $transaction->reference_number ?? 'None' }}</p>
                        <p><strong>Recorded At:</strong> {{ $transaction->created_at->format('F d, Y h:i A') }}</p>
                        <p><strong>Authorized By:</strong> {{ $transaction->user->name ?? 'System' }}</p>
                    </div>

                    <!-- Product Info Card -->
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-3">Product Information</h3>
                        <p><strong>Product Name:</strong> {{ $transaction->product->name ?? 'Deleted Product' }}</p>
                        <p><strong>Current Product Stock:</strong> {{ $transaction->product->quantity ?? 'N/A' }}</p>
                        <p><strong>Reorder Level:</strong> {{ $transaction->product->reorder_level ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Notes Section -->
                @if($transaction->notes)
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-2">Notes</h3>
                        <p class="text-gray-700">{{ $transaction->notes }}</p>
                    </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>