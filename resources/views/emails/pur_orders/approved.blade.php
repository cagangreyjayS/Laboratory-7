<x-mail::message>
# Purchase Order Approved

Good news! Purchase order **{{ $purchaseOrder->reference_number }}** has been approved and is ready to be sent to the supplier.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>