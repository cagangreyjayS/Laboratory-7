<x-mail::message>
# Purchase Order Created

A new purchase order **{{ $purchaseOrder->reference_number }}** has been created for supplier **{{ $purchaseOrder->supplier->company_name ?? 'N/A' }}**.

**Total Amount:** ${{ number_format($purchaseOrder->total_amount, 2) }}  
**Expected Delivery:** {{ $purchaseOrder->expected_delivery_date?->format('Y-m-d') ?? 'TBD' }}

<x-mail::button :url="route('purchase-orders.show', $purchaseOrder->id)">
View Purchase Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Student Portal
</x-mail::message>