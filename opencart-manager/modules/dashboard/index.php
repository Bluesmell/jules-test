<?php
// modules/dashboard/index.php
$page_title = "Sales Dashboard - OpenCart Manager";

// Fetch live sales metrics
$sales_metrics_data = [
    'daily' => get_sales_summary('daily'),
    'weekly' => get_sales_summary('weekly'),
    'monthly' => get_sales_summary('monthly'),
    'yearly' => get_sales_summary('yearly'),
];

// This structure is for the existing UI template.
// Adapt if get_sales_summary returns a different structure.
$sales_metrics = [
    'daily' => ['value' => DEFAULT_CURRENCY_SYMBOL . number_format($sales_metrics_data['daily']['total_revenue'] ?? 0, 2), 'count' => $sales_metrics_data['daily']['order_count'] ?? 0],
    'weekly' => ['value' => DEFAULT_CURRENCY_SYMBOL . number_format($sales_metrics_data['weekly']['total_revenue'] ?? 0, 2), 'count' => $sales_metrics_data['weekly']['order_count'] ?? 0],
    'monthly' => ['value' => DEFAULT_CURRENCY_SYMBOL . number_format($sales_metrics_data['monthly']['total_revenue'] ?? 0, 2), 'count' => $sales_metrics_data['monthly']['order_count'] ?? 0],
    'yearly' => ['value' => DEFAULT_CURRENCY_SYMBOL . number_format($sales_metrics_data['yearly']['total_revenue'] ?? 0, 2), 'count' => $sales_metrics_data['yearly']['order_count'] ?? 0],
];

// Fetch Top Selling Products
// Assuming default language ID is 1. This should ideally come from a language helper.
// For valid order statuses, let's assume 'Complete' (e.g., ID 5) for now.
// This should be configurable or dynamically fetched in a real app.
$default_language_id = (defined('DEFAULT_LANGUAGE_ID') ? DEFAULT_LANGUAGE_ID : 1); // Assuming a constant might be set elsewhere or use a fallback
$completed_order_status_id = 5; // Example for 'Complete'. This is a simplification.
                                 // A better approach is to get this ID from oc_order_status table by name.
                                 // Or, pass an array of IDs like [3, 5] for 'Processing', 'Complete'.
                                 // For now, passing an empty array to use the default 'order_status_id > 0' in the function.

$top_products_data = get_top_selling_products(5, $default_language_id, []); // Fetch top 5 for current lang

// The existing UI expects $top_products with 'name', 'quantity', 'revenue'
// Adapt the $top_products_data if needed, or ensure get_top_selling_products returns this structure.
// The current get_top_selling_products returns 'name', 'total_quantity_sold', 'total_revenue'.
$top_products = [];
if (!empty($top_products_data)) {
    foreach ($top_products_data as $p) {
        $top_products[] = [
            'name' => $p['name'],
            'quantity' => $p['total_quantity_sold'],
            'revenue' => DEFAULT_CURRENCY_SYMBOL . number_format($p['total_revenue'], 2) // Format revenue
        ];
    }
} else {
    // Ensure $top_products is an empty array if no data, to avoid errors in the foreach loop in the HTML
    $top_products = [];
}

// Fetch Order Status Breakdown
$default_language_id = (defined('DEFAULT_LANGUAGE_ID') ? DEFAULT_LANGUAGE_ID : 1); // Consistent with top_products
$order_status_data = get_order_status_breakdown($default_language_id);

$order_status_labels = [];
$order_status_counts = [];
$order_status_colors = [ // Predefined colors, can be expanded or dynamically generated
    'rgba(255, 99, 132, 0.7)',  // Red
    'rgba(54, 162, 235, 0.7)', // Blue
    'rgba(255, 206, 86, 0.7)', // Yellow
    'rgba(75, 192, 192, 0.7)', // Green
    'rgba(153, 102, 255, 0.7)',// Purple
    'rgba(255, 159, 64, 0.7)', // Orange
    'rgba(201, 203, 207, 0.7)', // Grey
    'rgba(130, 200, 100, 0.7)',// Light Green
    'rgba(240, 150, 200, 0.7)' // Pink
];

if (!empty($order_status_data)) {
    foreach ($order_status_data as $status) {
        $order_status_labels[] = $status['name'];
        $order_status_counts[] = (int)$status['order_count'];
    }
} else {
    // Provide default placeholder if no data, to prevent JS errors
    $order_status_labels = ['No Data'];
    $order_status_counts = [0];
}

// The old $order_statuses array is no longer needed for the chart.
// If a table view was also required, it could be populated from $order_status_data.

// Fetch Monthly Revenue for Chart (last 6 months)
// Using default 'order_status_id > 0' for valid sales for now.
$monthly_revenue_data = get_monthly_revenue_for_chart(6, []); 

$revenue_chart_labels = [];
$revenue_chart_values = [];

if (!empty($monthly_revenue_data)) {
    foreach ($monthly_revenue_data as $month_data) {
        $revenue_chart_labels[] = $month_data['month_year_label']; // e.g., "Jan 2023"
        $revenue_chart_values[] = $month_data['total_revenue'];
    }
} else {
    // Provide default placeholder if no data, to prevent JS errors
    // And ensure labels match the number of months requested for a consistent empty chart
    for ($i = 0; $i < 6; $i++) {
        $revenue_chart_labels[] = date('M Y', strtotime("-".(5-$i)." months"));
        $revenue_chart_values[] = 0;
    }
}

// This replaces the old $revenue_chart_data_labels and $revenue_chart_data_values placeholders
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sales_metrics['daily']['value']; ?> (<?php echo $sales_metrics['daily']['count']; ?> Orders)</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sales_metrics['weekly']['value']; ?> (<?php echo $sales_metrics['weekly']['count']; ?> Orders)</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sales_metrics['monthly']['value']; ?> (<?php echo $sales_metrics['monthly']['count']; ?> Orders)</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $sales_metrics['yearly']['value']; ?> (<?php echo $sales_metrics_yearly['count']; ?> Orders)</div>
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
                                    <span class="badge bg-primary rounded-pill"><?php echo $product['quantity']; ?> sold (<?php echo $product['revenue']; ?>)</span>
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
            data: { // <<<< MODIFY THIS PART
                labels: <?php echo json_encode($revenue_chart_labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($revenue_chart_values); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Optional: fill area under line
                    fill: true,
                    tension: 0.1
                }]
            }, // <<<< END OF MODIFIED PART
            options: { // ... existing options ...
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '<?php echo DEFAULT_CURRENCY_SYMBOL; ?>' + value.toLocaleString(); // Format Y-axis ticks as currency
                            }
                        }
                    } 
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '<?php echo DEFAULT_CURRENCY_SYMBOL; ?>' + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (document.getElementById('chartFallbackText')) { // Ensure fallback text element exists
         document.getElementById('chartFallbackText').style.display = 'block';
    }

    // Placeholder for Order Status Pie Chart
    const orderStatusCtx = document.getElementById('orderStatusPieChartPlaceholder');
     if (orderStatusCtx) {
        document.getElementById('pieChartFallbackText').style.display = 'none'; // Hide fallback if canvas exists
        new Chart(orderStatusCtx.getContext('2d'), {
            type: 'doughnut', // or 'pie'
            data: {
                labels: <?php echo json_encode($order_status_labels); ?>,
                datasets: [{
                    label: 'Order Status Breakdown',
                    data: <?php echo json_encode($order_status_counts); ?>,
                    backgroundColor: <?php echo json_encode(array_slice($order_status_colors, 0, count($order_status_labels))); ?>, // Use a slice of colors
                    hoverOffset: 4
                }]
            },
             options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top', // Or 'bottom', 'left', 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (document.getElementById('pieChartFallbackText')) { // Ensure fallback text element exists
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
