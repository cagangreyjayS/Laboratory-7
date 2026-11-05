<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Recent Purchase Orders</h6>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @forelse($recentOrders ?? [] as $order)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>#{{ $order->reference_number }}</span>
                    <span class="badge bg-secondary text-white">{{ ucfirst($order->status) }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted">No recent purchase orders.</li>
            @endforelse
        </ul>
    </div>
</div>