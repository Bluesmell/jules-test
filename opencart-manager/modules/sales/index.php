<?php
// modules/sales/index.php
$page_title = "Sales - Order Listing - OpenCart Manager";

// Default pagination and sorting parameters (will be made dynamic later)
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Orders per page
$offset = ($current_page - 1) * $limit;

// Default sort (can be changed based on user input later)
$sort_by = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'date_desc'; 

// Filters (will be implemented later)
$filters = []; 
// Example: $filters['order_status_id'] = 5;
// Example: $filters['customer_name'] = 'John Doe';

// Fetch orders from the database
// Assuming default language ID is 1 for status names. This should be dynamic later.
$default_language_id = (defined('DEFAULT_LANGUAGE_ID') ? DEFAULT_LANGUAGE_ID : 1); 
$orders_list = get_orders_list($filters, $sort_by, $limit, $offset, $default_language_id);
$total_orders = get_total_orders_count($filters); // We'll need this function too

// The actual display (table, pagination) will be added in the next plan step.
// For now, let's just confirm data is being fetched (optional: var_dump for testing by developer)
/*
if ($orders_list === false) {
    echo "<div class='alert alert-danger'>Error fetching orders.</div>";
} elseif (empty($orders_list)) {
    echo "<div class='alert alert-info'>No orders found matching your criteria.</div>";
} else {
    // echo "<p>Fetched " . count($orders_list) . " orders out of " . $total_orders . " total.</p>";
    // echo "<pre>"; print_r($orders_list); echo "</pre>"; // For debugging during development
}
*/
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-dollar-sign me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <!-- Placeholder for Add Order or Export buttons if needed later -->
            <!-- <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Add New Order</button> -->
        </div>
    </div>

    <!-- Filters UI (Placeholder for now, functionality in later steps) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-1"></i>Filter Orders</h6>
        </div>
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="module" value="sales">
                <div class="col-md-3">
                    <label for="orderIdSearch" class="form-label">Order ID</label>
                    <input type="text" class="form-control form-control-sm" id="orderIdSearch" name="filter_order_id" placeholder="Enter Order ID">
                </div>
                <div class="col-md-3">
                    <label for="customerNameSearch" class="form-label">Customer Name</label>
                    <input type="text" class="form-control form-control-sm" id="customerNameSearch" name="filter_customer_name" placeholder="Enter Customer Name">
                </div>
                <div class="col-md-3">
                    <label for="orderStatusFilter" class="form-label">Order Status</label>
                    <select id="orderStatusFilter" name="filter_order_status_id" class="form-select form-select-sm">
                        <option selected value="">All Statuses</option>
                        <!-- Options to be populated dynamically later -->
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="dateRangeFilter" class="form-label">Date Range</label>
                    <input type="text" class="form-control form-control-sm" id="dateRangeFilter" name="filter_date_range" placeholder="Select Date Range"> <!-- Date picker to be added later -->
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-sm btn-info w-100"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Order List Table (Structure only for now, content in next step) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul me-1"></i>Orders</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="order-list-table-placeholder">
                <?php if ($orders_list === false): ?>
                    <div class="alert alert-danger">Error fetching orders. Please check system logs.</div>
                <?php elseif (empty($orders_list)): ?>
                    <div class="alert alert-info">No orders found matching your criteria.</div>
                <?php else: ?>
                    <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders_list as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><span class="badge" style="background-color: #<?php echo substr(md5($order['status_name']), 0, 6); ?>; color: white;"><?php echo htmlspecialchars($order['status_name']); ?></span></td>
                                    <td class="text-end"><?php echo DEFAULT_CURRENCY_SYMBOL . number_format($order['total'], 2); ?> <?php echo htmlspecialchars($order['currency_code']); ?></td>
                                    <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($order['date_added']))); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="alert('View details for Order ID <?php echo $order['order_id']; ?> - Placeholder');" title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- Add more actions later, e.g., edit, invoice -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div id="order-list-pagination-placeholder" class="mt-3">
                <?php if ($total_orders > 0 && $limit > 0): ?>
                    <nav aria-label="Orders navigation">
                        <ul class="pagination justify-content-center">
                            <?php
                            $total_pages = ceil($total_orders / $limit);
                            $max_visible_pages = 5; // Max page numbers to show excluding prev/next
                            $start_page = 1;
                            $end_page = $total_pages;

                            if ($total_pages > $max_visible_pages) {
                                $start_page = max(1, $current_page - floor($max_visible_pages / 2));
                                $end_page = $start_page + $max_visible_pages - 1;
                                if ($end_page > $total_pages) {
                                    $end_page = $total_pages;
                                    $start_page = max(1, $end_page - $max_visible_pages + 1);
                                }
                            }
                            ?>

                            <!-- Previous Page Link -->
                            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo BASE_URL; ?>index.php?module=sales&page=<?php echo $current_page - 1; ?>&sort=<?php echo $sort_by; /* Add other filters here */ ?>">Previous</a>
                            </li>

                            <?php if ($start_page > 1): ?>
                                <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>index.php?module=sales&page=1&sort=<?php echo $sort_by; ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo BASE_URL; ?>index.php?module=sales&page=<?php echo $i; ?>&sort=<?php echo $sort_by; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>index.php?module=sales&page=<?php echo $total_pages; ?>&sort=<?php echo $sort_by; ?>"><?php echo $total_pages; ?></a></li>
                            <?php endif; ?>
                            
                            <!-- Next Page Link -->
                            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo BASE_URL; ?>index.php?module=sales&page=<?php echo $current_page + 1; ?>&sort=<?php echo $sort_by; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <p class="text-center text-muted small">Showing <?php echo count($orders_list); ?> orders of <?php echo $total_orders; ?> total (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>).</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
