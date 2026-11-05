<x-mail::message>
# Purchase Order Sent

Purchase order **{{ $purchaseOrder->reference_number }}** has been dispatched to **{{ $purchaseOrder->supplier->company_name ?? 'Supplier' }}**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>