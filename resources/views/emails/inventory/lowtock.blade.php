<x-mail::message>
# Low Stock Warning

Product **{{ $product->name }}** (SKU: {{ $product->sku }}) has reached a low stock level.
* **Current Quantity:** {{ $product->quantity }}
* **Reorder Threshold:** {{ $product->reorder_level }}

Please review your procurement options.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>