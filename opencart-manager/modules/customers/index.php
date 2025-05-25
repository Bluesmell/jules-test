<?php
// modules/customers/index.php
$page_title = "Customer Management - OpenCart Manager";

// Default pagination and sorting parameters
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Customers per page
$offset = ($current_page - 1) * $limit;
$sort_by = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'name_asc'; 

// Filters (UI elements will connect to these $_GET params)
$filters = [];
if (!empty($_GET['filter_name'])) {
    $filters['name'] = sanitize_input($_GET['filter_name']);
}
if (!empty($_GET['filter_email'])) {
    $filters['email'] = sanitize_input($_GET['filter_email']);
}
if (isset($_GET['filter_customer_oc_status']) && $_GET['filter_customer_oc_status'] !== '') {
    $filters['customer_oc_status'] = sanitize_input($_GET['filter_customer_oc_status']);
}

// Fetch customer data from the database
$customers_list = get_customers_list($filters, $sort_by, $limit, $offset);
$total_customers = get_total_customers_count($filters);

// Remove or comment out old placeholder $customers array
// $customer_segments = ['New', 'Regular', 'VIP', 'Lapsed']; // Keep if still used for filter UI, or fetch dynamically
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><i class="fas fa-plus me-1"></i> Add New Customer</button>
        </div>
    </div>

    <!-- Search and Filter Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-1"></i>Filter Customers</h6>
        </div>
        <div class="card-body">
            <form class="row g-3 align-items-center" method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="module" value="customers">
                <div class="col-md-3">
                    <label for="filterName" class="form-label">Customer Name</label>
                    <input type="text" class="form-control form-control-sm" id="filterName" name="filter_name" placeholder="Enter Name" value="<?php echo isset($filters['name']) ? htmlspecialchars($filters['name']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="filterEmail" class="form-label">Email</label>
                    <input type="text" class="form-control form-control-sm" id="filterEmail" name="filter_email" placeholder="Enter Email" value="<?php echo isset($filters['email']) ? htmlspecialchars($filters['email']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <label for="filterCustomerOcStatus" class="form-label">OC Status</label>
                    <select id="filterCustomerOcStatus" name="filter_customer_oc_status" class="form-select form-select-sm">
                        <option value="" <?php echo (!isset($filters['customer_oc_status']) || $filters['customer_oc_status'] === '') ? 'selected' : ''; ?>>Any Status</option>
                        <option value="1" <?php echo (isset($filters['customer_oc_status']) && $filters['customer_oc_status'] === '1') ? 'selected' : ''; ?>>Enabled</option>
                        <option value="0" <?php echo (isset($filters['customer_oc_status']) && $filters['customer_oc_status'] === '0') ? 'selected' : ''; ?>>Disabled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="customerSort" class="form-label">Sort By</label>
                    <select id="customerSort" name="sort" class="form-select form-select-sm">
                        <option value="name_asc" <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo ($sort_by === 'name_desc') ? 'selected' : ''; ?>>Name (Z-A)</option>
                        <option value="email_asc" <?php echo ($sort_by === 'email_asc') ? 'selected' : ''; ?>>Email (A-Z)</option>
                        <option value="email_desc" <?php echo ($sort_by === 'email_desc') ? 'selected' : ''; ?>>Email (Z-A)</option>
                        <option value="date_added_desc" <?php echo ($sort_by === 'date_added_desc') ? 'selected' : ''; ?>>Date Added (Newest)</option>
                        <option value="date_added_asc" <?php echo ($sort_by === 'date_added_asc') ? 'selected' : ''; ?>>Date Added (Oldest)</option>
                    </select>
                </div>
                <div class="col-md-1 align-self-end"> <!-- Ensure button aligns with other fields if label is present, or use this for spacing -->
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer List Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul me-1"></i>Customer List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="customer-list-table-placeholder"> <!-- Ensure this ID is unique if needed, or use class -->
                <?php if ($customers_list === false): // Should not happen if functions return [] on error ?>
                    <div class="alert alert-danger">Error fetching customer data. Please check system logs.</div>
                <?php elseif (empty($customers_list)): ?>
                    <div class="alert alert-info">No customers found matching your criteria.</div>
                <?php else: ?>
                    <table class="table table-bordered table-striped table-hover" id="customerTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>OC Status</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers_list as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['telephone']); ?></td>
                                    <td>
                                        <?php if ($customer['customer_oc_status'] == 1): ?>
                                            <span class="badge bg-success">Enabled</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($customer['date_added']))); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="alert('View details for Customer ID <?php echo $customer['customer_id']; ?> - Placeholder');" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" onclick="alert('View orders for Customer ID <?php echo $customer['customer_id']; ?> - Placeholder');" title="View Orders">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                        <!-- Add more actions later, e.g., edit customer -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="mt-3" id="customer-list-pagination-placeholder">
                <?php if ($total_customers > 0 && $limit > 0): ?>
                    <nav aria-label="Customers navigation">
                        <ul class="pagination justify-content-center">
                            <?php
                            $total_pages = ceil($total_customers / $limit);
                            
                            $filter_params = []; // To hold current filters for pagination links
                            if (!empty($filters['name'])) $filter_params['filter_name'] = $filters['name'];
                            if (!empty($filters['email'])) $filter_params['filter_email'] = $filters['email'];
                            if (isset($filters['customer_oc_status']) && $filters['customer_oc_status'] !== '') $filter_params['customer_oc_status'] = $filters['customer_oc_status'];

                            // Previous Page
                            if ($current_page > 1) {
                                $prev_page_params = http_build_query(array_merge($filter_params, ['module' => 'customers', 'page' => $current_page - 1, 'sort' => $sort_by]));
                                echo "<li class='page-item'><a class='page-link' href='" . BASE_URL . "index.php?{$prev_page_params}'>Previous</a></li>";
                            } else {
                                echo "<li class='page-item disabled'><span class='page-link'>Previous</span></li>";
                            }

                            // Page Numbers (simplified version, can be enhanced like in Sales module if many pages are expected)
                            for ($i = 1; $i <= $total_pages; $i++) {
                                $page_params = http_build_query(array_merge($filter_params, ['module' => 'customers', 'page' => $i, 'sort' => $sort_by]));
                                if ($i == $current_page) {
                                    echo "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
                                } else {
                                     // Simple pagination: show first, last, and a few around current.
                                    if ($total_pages > 10 && abs($i - $current_page) > 2 && $i != 1 && $i != $total_pages && !($i > $current_page - 2 && $i < $current_page + 2) ) {
                                        if (!isset($ellipsis_shown_before_cust) && $i < $current_page) { echo "<li class='page-item disabled'><span class='page-link'>...</span></li>"; $ellipsis_shown_before_cust = true; }
                                        if (!isset($ellipsis_shown_after_cust) && $i > $current_page) { echo "<li class='page-item disabled'><span class='page-link'>...</span></li>"; $ellipsis_shown_after_cust = true; }
                                        continue;
                                    }
                                    echo "<li class='page-item'><a class='page-link' href='" . BASE_URL . "index.php?{$page_params}'>{$i}</a></li>";
                                }
                            }
                            unset($ellipsis_shown_before_cust, $ellipsis_shown_after_cust);


                            // Next Page
                            if ($current_page < $total_pages) {
                                $next_page_params = http_build_query(array_merge($filter_params, ['module' => 'customers', 'page' => $current_page + 1, 'sort' => $sort_by]));
                                echo "<li class='page-item'><a class='page-link' href='" . BASE_URL . "index.php?{$next_page_params}'>Next</a></li>";
                            } else {
                                echo "<li class='page-item disabled'><span class='page-link'>Next</span></li>";
                            }
                            ?>
                        </ul>
                    </nav>
                    <p class="text-center text-muted small">Showing <?php echo count($customers_list); ?> of <?php echo $total_customers; ?> total customers (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>).</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Advanced Analytics Placeholders -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-1"></i>Customer Lifetime Value (CLV)</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">CLV calculations will be displayed here.</p>
                    <h3 class="display-6">N/A</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users-slash me-1"></i>Customer Segmentation</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Customer segments overview will be displayed here.</p>
                     <div id="customerSegmentChartPlaceholder" style="height: 150px; background-color: #f8f9fc; display:flex; align-items:center; justify-content:center;">
                        <small>Chart/Data Placeholder</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-redo-alt me-1"></i>Order Frequency Analysis</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Order frequency insights will be displayed here.</p>
                     <div id="orderFrequencyChartPlaceholder" style="height: 150px; background-color: #f8f9fc; display:flex; align-items:center; justify-content:center;">
                        <small>Chart/Data Placeholder</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal (Placeholder) -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCustomerModalLabel"><i class="fas fa-user-plus me-1"></i>Add New Customer (Placeholder)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Form for adding a new customer will be here.</p>
        <!-- Basic form fields example -->
        <form>
          <div class="mb-3">
            <label for="customerName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="customerName" required>
          </div>
          <div class="mb-3">
            <label for="customerEmail" class="form-label">Email address</label>
            <input type="email" class="form-control" id="customerEmail" required>
          </div>
           <div class="mb-3">
            <label for="customerPhone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="customerPhone">
          </div>
          <button type="submit" class="btn btn-primary">Save Customer</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
// Add any specific JS for customer management here if needed.
// For example, initializing a JS library for table sorting/pagination,
// or handling filter form submission via AJAX.
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.querySelector('.card-body form');
    if(filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Customer filtering would be applied here. (Placeholder)');
            // In a real app, you'd gather form data and refresh the table or make an AJAX request.
        });
    }
});
</script>
