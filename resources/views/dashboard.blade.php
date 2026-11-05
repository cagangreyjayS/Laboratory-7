@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- SIDEBAR --}}
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="mb-4">Navigation</h5>

            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}"
                   class="nav-link active">🏠 Dashboard</a>

                <a href="{{ route('inventory.index') }}"
                   class="nav-link">📦 Inventory</a>

                {{-- NEW LABORATORY 7 NAVIGATION LINKS --}}
                <a href="{{ route('suppliers.index') }}"
                   class="nav-link">🏭 Suppliers</a>

                <a href="{{ route('purchase-orders.index') }}"
                   class="nav-link">📋 Purchase Orders</a>

                <a href="{{ route('suppliers.performance-report') }}"
                   class="nav-link">📊 Supplier Performance</a>

                <a href="#profileSettings"
                   class="nav-link">👤 Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="nav-link text-start w-100 border-0 bg-transparent">
                        🚪 Log Out
                    </button>
                </form>
            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="col-md-9 col-lg-10 p-4">

            {{-- PROFILE --}}
            <section id="profileSettings" class="mb-5">
                <h2 class="mb-4">Profile</h2>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Student Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student Name</label>
                                <input class="form-control"
                                       value="{{ auth()->user()->name ?? 'Admin' }}"
                                       readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Student ID</label>
                                <input class="form-control"
                                       value="2026-001" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Academic Year</label>
                                <input class="form-control"
                                       value="2026-2027" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Year Level</label>
                                <input class="form-control"
                                       value="3rd Year" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Program</label>
                                <input class="form-control"
                                       value="Bachelor of Science in Information Technology"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- DASHBOARD --}}
            <section id="dashboardSettings">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        Good {{ now()->hour >= 5 && now()->hour < 12 ? 'Morning' : (now()->hour < 17 && now()->hour >= 12 ? 'Afternoon' : (now()->hour < 21 && now()->hour >= 17 ? 'Evening' : 'Night')) }},
                        {{ auth()->user()->name ?? 'Admin' }}!
                    </h2>
                    <!-- Real-time Last Updated Timestamp -->
                    <div class="text-sm text-muted">
                        Last Updated: <span id="last-updated">Loading...</span>
                    </div>
                </div>

                {{-- NEW: LABORATORY 7 PROCUREMENT WIDGETS GRID --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        @include('widgets.pending-orders')
                    </div>
                    <div class="col-md-6 col-xl-3">
                        @include('widgets.reorder-alerts')
                    </div>
                    <div class="col-md-6 col-xl-3">
                        @include('widgets.recent-orders')
                    </div>
                    <div class="col-md-6 col-xl-3">
                        @include('widgets.supplier-performance')
                    </div>
                </div>

                {{-- REAL-TIME INVENTORY STATS CARDS --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="text-muted">📦 Total Products</h6>
                                <p id="stat-total-products" class="card-text display-6 fw-bold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="text-muted">⚠️ Low Stock Items</h6>
                                <p id="stat-low-stock" class="card-text display-6 fw-bold text-warning">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="text-muted">❌ Out of Stock</h6>
                                <p id="stat-out-of-stock" class="card-text display-6 fw-bold text-danger">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="text-muted">🔄 Total Transactions</h6>
                                <p id="stat-total-transactions" class="card-text display-6 fw-bold text-primary">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PERFORMANCE ALERT --}}
                @if(isset($lowPerformanceCount) && $lowPerformanceCount > 0)
                    <div class="alert alert-warning mb-4">
                        <strong>Performance Alert:</strong>
                        {{ $lowPerformanceCount }} scored activity item(s)
                        are below 90. Review the highlighted rows.
                    </div>
                @endif

                {{-- 3 CHARTS SECTION (CARRIED OVER FROM LAB 5) --}}
                <div class="row g-4 mb-4">
                    <!-- Chart 1: Bar Chart -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3">
                            <h6 class="fw-bold mb-3 text-secondary">Inventory Status</h6>
                            <div style="height: 220px;">
                                <canvas id="inventoryBarChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 2: Doughnut Chart -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3">
                            <h6 class="fw-bold mb-3 text-secondary">Stock Distribution</h6>
                            <div style="height: 220px;">
                                <canvas id="stockDoughnutChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 3: Line Chart -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3">
                            <h6 class="fw-bold mb-3 text-secondary">Transaction Trends</h6>
                            <div style="height: 220px;">
                                <canvas id="activityLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEARCH AND FILTERS --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="searchInput" class="form-label">
                                    Search
                                </label>
                                <input id="searchInput"
                                       class="form-control"
                                       placeholder="Search inventory or activities...">
                            </div>

                            <div class="col-md-3">
                                <label for="categoryFilter" class="form-label">
                                    Category
                                </label>
                                <select id="categoryFilter"
                                        class="form-select">
                                    <option value="all">All Categories</option>
                                    @if(isset($products))
                                        @foreach($products->pluck('category')->filter()->unique()->sort() as $category)
                                            <option value="{{ $category }}">
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">
                                    Status
                                </label>
                                <select id="statusFilter" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button id="resetFilters"
                                        class="btn btn-secondary w-100">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RECENT TRANSACTIONS / MOVEMENTS SECTION --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Recent Stock Movements</h5>
                            <span class="badge bg-secondary">Live Polling Active</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>User</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-transactions-body">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Loading recent movements...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- STUDENT ACTIVITIES TABLE --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Student Activities</h5>

                            <div>
                                <span class="me-3">
                                    Results:
                                    <strong id="resultCount">
                                        {{ isset($products) ? $products->count() : 0 }}
                                    </strong>
                                </span>
                                <a href="{{ route('student-activities.export') }}"
                                        class="btn btn-sm btn-primary">
                                    Export CSV
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Activity</th>
                                        <th>Status</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody id="activity-table-body">
                                    @if(isset($products))
                                        @foreach($products as $product)
                                            <tr>
                                                <td>{{ $product->created_at ? $product->created_at->format('Y-m-d') : 'N/A' }}</td>
                                                <td>{{ $product->category ?? 'General' }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $product->status === 'completed' ? 'success' : ($product->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($product->status ?? 'pending') }}
                                                    </span>
                                                </td>
                                                <td>{{ $product->score ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Chart.js CDN & Initialization Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Chart Initializations ---
    const barCtx = document.getElementById('inventoryBarChart').getContext('2d');
    const inventoryBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Total', 'Low Stock', 'Out of Stock'],
            datasets: [{
                label: 'Products',
                data: [0, 0, 0],
                backgroundColor: ['#800776', '#C08552', '#5E3023'],
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    const doughnutCtx = document.getElementById('stockDoughnutChart').getContext('2d');
    const stockDoughnutChart = new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Normal Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: ['#800776', '#C08552', '#895737']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    const lineCtx = document.getElementById('activityLineChart').getContext('2d');
    const activityLineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Stock Movements',
                data: [2, 5, 3, 8, 4, 6, 9],
                borderColor: '#800776',
                backgroundColor: 'rgba(128, 7, 118, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // 1. Fetch and update dashboard statistics & charts dynamically via AJAX
    function fetchDashboardStats() {
        fetch('/inventory/stats')
            .then(response => response.json())
            .then(data => {
                const totalProducts = data.total_products ?? 0;
                const lowStockCount = data.low_stock_count ?? 0;
                const outOfStockCount = data.out_of_stock_count ?? 0;
                const normalStockCount = Math.max(0, totalProducts - (lowStockCount + outOfStockCount));
                const totalTransactions = data.recent_transactions ?? 0;

                // Update text cards
                document.getElementById('stat-total-products').textContent = totalProducts;
                document.getElementById('stat-low-stock').textContent = lowStockCount;
                document.getElementById('stat-total-transactions').textContent = totalTransactions;
                
                if (document.getElementById('stat-out-of-stock')) {
                    document.getElementById('stat-out-of-stock').textContent = outOfStockCount;
                }

                // Update Bar Chart Data
                inventoryBarChart.data.datasets[0].data = [totalProducts, lowStockCount, outOfStockCount];
                inventoryBarChart.update();

                // Update Doughnut Chart Data
                stockDoughnutChart.data.datasets[0].data = [normalStockCount, lowStockCount, outOfStockCount];
                stockDoughnutChart.update();

                // Update timestamp
                document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
            })
            .catch(error => console.error('Error fetching inventory stats:', error));
    }

    // 2. Fetch and render Recent Stock Movements table dynamically
    function fetchRecentMovements() {
        fetch('/inventory/recent-transactions')
            .then(response => response.json())
            .then(transactions => {
                const tbody = document.getElementById('recent-transactions-body');
                tbody.innerHTML = ''; // Clear loading message

                if (!transactions || transactions.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No recent stock movements recorded yet.</td></tr>`;
                    return;
                }

                transactions.forEach(tx => {
                    const productName = tx.inventory_product ? tx.inventory_product.name : 'Product unavailable';
                    const badgeClass = tx.type === 'stock_in' ? 'bg-success' : 'bg-danger';
                    const typeLabel = tx.type === 'stock_in' ? 'STOCK IN' : 'STOCK OUT';
                    const userName = tx.user ? tx.user.name : 'System/Admin';
                    const timeFormatted = tx.created_at ? new Date(tx.created_at).toLocaleString() : 'N/A';

                    const row = `
                        <tr>
                            <td>${productName}</td>
                            <td><span class="badge ${badgeClass}">${typeLabel}</span></td>
                            <td>${tx.quantity}</td>
                            <td>${userName}</td>
                            <td>${timeFormatted}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => {
                console.error('Error fetching recent movements:', error);
                document.getElementById('recent-transactions-body').innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load recent movements.</td></tr>`;
            });
    }

    // Initial load and live polling interval (every 10 seconds)
    fetchDashboardStats();
    fetchRecentMovements();
    setInterval(() => {
        fetchDashboardStats();
        fetchRecentMovements();
    }, 10000);
});
</script>
@endsection