<?php
// modules/dashboard/index.php
$page_title = "Sales Dashboard - OpenCart Manager";

// In a real scenario, you would fetch this data from the database
// For Phase 1, we'll use placeholder data.
$sales_metrics = [
    'daily' => ['value' => '0.00', 'count' => 0],
    'weekly' => ['value' => '0.00', 'count' => 0],
    'monthly' => ['value' => '0.00', 'count' => 0],
    'yearly' => ['value' => '0.00', 'count' => 0],
];

$top_products = [
    ['name' => 'Placeholder Product 1', 'quantity' => 0, 'revenue' => '0.00'],
    ['name' => 'Placeholder Product 2', 'quantity' => 0, 'revenue' => '0.00'],
    ['name' => 'Placeholder Product 3', 'quantity' => 0, 'revenue' => '0.00'],
];

$order_statuses = [
    ['status' => 'Pending', 'count' => 0, 'class' => 'warning'],
    ['status' => 'Processing', 'count' => 0, 'class' => 'info'],
    ['status' => 'Shipped', 'count' => 0, 'class' => 'primary'],
    ['status' => 'Complete', 'count' => 0, 'class' => 'success'],
    ['status' => 'Canceled', 'count' => 0, 'class' => 'danger'],
    ['status' => 'Refunded', 'count' => 0, 'class' => 'secondary'],
];

// Data for charts (placeholder)
$revenue_chart_data_labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
$revenue_chart_data_values = [0, 0, 0, 0, 0, 0];

?>

<div class="container-fluid">
    <!-- Page Title & Export Buttons -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tachometer-alt me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <button class="btn btn-sm btn-outline-secondary me-2" id="exportCsvBtn"><i class="fas fa-file-csv me-1"></i> Export CSV</button>
            <button class="btn btn-sm btn-outline-secondary" id="exportPdfBtn"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
        </div>
    </div>

    <!-- Sales Metrics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sales (Daily)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $sales_metrics['daily']['value']; ?> (<?php echo $sales_metrics['daily']['count']; ?> Orders)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sales (Weekly)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $sales_metrics['weekly']['value']; ?> (<?php echo $sales_metrics['weekly']['count']; ?> Orders)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sales (Monthly)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $sales_metrics['monthly']['value']; ?> (<?php echo $sales_metrics['monthly']['count']; ?> Orders)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sales (Yearly)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $sales_metrics['yearly']['value']; ?> (<?php echo $sales_metrics['yearly']['count']; ?> Orders)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-area me-1"></i>Revenue Overview (Placeholder)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px; background-color: #f8f9fc; display:flex; align-items:center; justify-content:center;">
                        <canvas id="revenueChartPlaceholder"></canvas>
                        <!-- Placeholder text if Chart.js isn't loaded or initialized -->
                        <p class="text-muted" id="chartFallbackText">Chart will be rendered here.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-1"></i>Order Status Breakdown (Placeholder)</h6>
                </div>
                <div class="card-body">
                     <div id="orderStatusChartPlaceholder" style="height: 320px; background-color: #f8f9fc; display:flex; align-items:center; justify-content:center;">
                        <canvas id="orderStatusPieChartPlaceholder"></canvas>
                        <p class="text-muted" id="pieChartFallbackText">Pie chart will be rendered here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products & Customer Patterns Row -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-star me-1"></i>Top-Selling Products (Placeholder)</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($top_products)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($top_products as $product): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                    <span class="badge bg-primary rounded-pill"><?php echo $product['quantity']; ?> sold ($<?php echo $product['revenue']; ?>)</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No top products data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users-cog me-1"></i>Customer Purchase Patterns (Placeholder)</h6>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted">Customer purchase pattern analysis will be displayed here.</p>
                    <div style="height: 150px; background-color: #f8f9fc; display:flex; align-items:center; justify-content:center;">
                        <small>Graph/Data Placeholder</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Placeholder for Revenue Chart
    const revCtx = document.getElementById('revenueChartPlaceholder');
    if (revCtx) {
        document.getElementById('chartFallbackText').style.display = 'none'; // Hide fallback if canvas exists
        new Chart(revCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($revenue_chart_data_labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($revenue_chart_data_values); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    } else {
         document.getElementById('chartFallbackText').style.display = 'block';
    }

    // Placeholder for Order Status Pie Chart
    const orderStatusCtx = document.getElementById('orderStatusPieChartPlaceholder');
     if (orderStatusCtx) {
        document.getElementById('pieChartFallbackText').style.display = 'none'; // Hide fallback if canvas exists
        new Chart(orderStatusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($order_statuses, 'status')); ?>,
                datasets: [{
                    label: 'Order Status',
                    data: <?php echo json_encode(array_column($order_statuses, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 159, 64, 0.7)', // Pending
                        'rgba(54, 162, 235, 0.7)', // Processing
                        'rgba(75, 192, 192, 0.7)', // Shipped
                        'rgba(153, 102, 255, 0.7)',// Complete
                        'rgba(255, 99, 132, 0.7)', // Cancelled
                        'rgba(201, 203, 207, 0.7)' // Refunded
                    ],
                }]
            },
             options: { responsive: true, maintainAspectRatio: false }
        });
    } else {
        document.getElementById('pieChartFallbackText').style.display = 'block';
    }

    // Placeholder for export buttons
    document.getElementById('exportCsvBtn').addEventListener('click', function() {
        alert('CSV Export functionality will be implemented in a later phase.');
    });
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        alert('PDF Export functionality will be implemented in a later phase.');
    });
});
</script>
