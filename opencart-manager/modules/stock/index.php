<?php
// modules/stock/index.php
$page_title = "Stock Management - OpenCart Manager";

// Default pagination and sorting parameters
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Products per page
$offset = ($current_page - 1) * $limit;
$sort_by = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'name_asc'; 
$default_language_id = (defined('DEFAULT_LANGUAGE_ID') ? DEFAULT_LANGUAGE_ID : 1);

// Filters (UI elements for these will be added/connected later)
$filters = [];
if (!empty($_GET['filter_product_name'])) {
    $filters['product_name'] = sanitize_input($_GET['filter_product_name']);
}
if (!empty($_GET['filter_sku_model'])) {
    $filters['sku_model'] = sanitize_input($_GET['filter_sku_model']);
}
if (isset($_GET['filter_product_oc_status']) && $_GET['filter_product_oc_status'] !== '') {
    $filters['product_oc_status'] = sanitize_input($_GET['filter_product_oc_status']);
}
// Add more filters as UI is built

// Fetch inventory data from the database
$current_inventory_list = get_inventory_levels($filters, $sort_by, $limit, $offset, $default_language_id);
$total_products = get_total_products_count($filters, $default_language_id);

// Placeholder data for other sections (will be replaced or removed)
// $low_stock_alerts_threshold = 15; 
// $low_stock_products = []; // This will be derived from $current_inventory_list or a separate query later
// $stock_movement_history = [ /* ... existing placeholder ... */ ];

$low_stock_threshold = 10; // Define default low stock threshold

// Populate low stock alerts based on the current page's inventory list
$low_stock_alert_products = [];
if (!empty($current_inventory_list)) {
    foreach ($current_inventory_list as $product) {
        if ($product['quantity'] > 0 && $product['quantity'] < $low_stock_threshold) {
            $low_stock_alert_products[] = $product;
        }
    }
}
// Note: This filters only the current page of inventory for the alert section.
// For a site-wide low stock alert section, a separate query fetching ALL low stock products
// (not just the current page of the main inventory list) would be better.
// For this plan step, filtering the current page's list is acceptable.
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-boxes me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal"><i class="fas fa-edit me-1"></i> Bulk Stock Update</button>
            <button class="btn btn-sm btn-outline-secondary" title="Inventory Valuation Report" id="inventoryValuationReportBtn"><i class="fas fa-file-alt me-1"></i> Inventory Valuation Report</button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-1"></i>Filter Inventory</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo BASE_URL; ?>index.php" class="row g-3 align-items-end">
                <input type="hidden" name="module" value="stock">
                <div class="col-md-3">
                    <label for="filterProductName" class="form-label">Product Name</label>
                    <input type="text" class="form-control form-control-sm" id="filterProductName" name="filter_product_name" placeholder="Enter product name..." value="<?php echo isset($filters['product_name']) ? htmlspecialchars($filters['product_name']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="filterSkuModel" class="form-label">SKU / Model</label>
                    <input type="text" class="form-control form-control-sm" id="filterSkuModel" name="filter_sku_model" placeholder="Enter SKU or Model..." value="<?php echo isset($filters['sku_model']) ? htmlspecialchars($filters['sku_model']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="filterProductOcStatus" class="form-label">OpenCart Status</label>
                    <select id="filterProductOcStatus" name="filter_product_oc_status" class="form-select form-select-sm">
                        <option value="" <?php echo (!isset($filters['product_oc_status']) || $filters['product_oc_status'] === '') ? 'selected' : ''; ?>>Any Status</option>
                        <option value="1" <?php echo (isset($filters['product_oc_status']) && $filters['product_oc_status'] === '1') ? 'selected' : ''; ?>>Enabled</option>
                        <option value="0" <?php echo (isset($filters['product_oc_status']) && $filters['product_oc_status'] === '0') ? 'selected' : ''; ?>>Disabled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterSortBy" class="form-label">Sort By</label>
                    <select id="filterSortBy" name="sort" class="form-select form-select-sm">
                        <option value="name_asc" <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo ($sort_by === 'name_desc') ? 'selected' : ''; ?>>Name (Z-A)</option>
                        <option value="quantity_asc" <?php echo ($sort_by === 'quantity_asc') ? 'selected' : ''; ?>>Quantity (Low-High)</option>
                        <option value="quantity_desc" <?php echo ($sort_by === 'quantity_desc') ? 'selected' : ''; ?>>Quantity (High-Low)</option>
                        <option value="sku_asc" <?php echo ($sort_by === 'sku_asc') ? 'selected' : ''; ?>>SKU/Model (A-Z)</option>
                        <option value="sku_desc" <?php echo ($sort_by === 'sku_desc') ? 'selected' : ''; ?>>SKU/Model (Z-A)</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label> <!-- Spacer for alignment -->
                    <button type="submit" class="btn btn-sm btn-info w-100"><i class="fas fa-search"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Low Stock Alerts Section -->
    <?php if (!empty($low_stock_alert_products)): ?>
    <div class="row mb-4"> <!-- Added mb-4 for spacing -->
        <div class="col-12">
            <div class="alert alert-warning">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts (Threshold: <?php echo $low_stock_threshold; ?>)</h5>
                <ul class="list-unstyled mb-0"> <?php // Changed to list-unstyled for cleaner look if preferred ?>
                    <?php foreach ($low_stock_alert_products as $product): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                            (SKU/Model: <?php echo htmlspecialchars(!empty($product['sku']) ? $product['sku'] : $product['model']); ?>)
                            - Current Quantity: <span class="fw-bold"><?php echo $product['quantity']; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Current Inventory Levels -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt me-1"></i>Current Inventory Levels</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php if ($current_inventory_list === false): ?>
                    <div class="alert alert-danger">Error fetching inventory data. Please check system logs.</div>
                <?php elseif (empty($current_inventory_list)): ?>
                    <div class="alert alert-info">No products found matching your criteria.</div>
                <?php else: ?>
                    <table class="table table-bordered table-striped table-hover" id="inventoryTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>SKU / Model</th>
                                <th class="text-end">Quantity</th>
                                <th>OC Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($current_inventory_list as $product): ?>
                                <tr class="<?php echo ($product['quantity'] <= 0) ? 'table-danger' : (($product['quantity'] > 0 && $product['quantity'] < ($low_stock_threshold ?? 10)) ? 'table-warning' : ''); ?>">
                                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars(!empty($product['sku']) ? $product['sku'] : $product['model']); ?></td>
                                    <td class="text-end"><?php echo htmlspecialchars($product['quantity']); ?></td>
                                    <td>
                                        <?php if ($product['product_oc_status'] == 1): ?>
                                            <span class="badge bg-success">Enabled</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="alert('View details for Product ID <?php echo $product['product_id']; ?> - Placeholder');" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="alert('Adjust stock for Product ID <?php echo $product['product_id']; ?> - Placeholder');" title="Adjust Stock">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <?php if ($total_products > 0 && $limit > 0): ?>
                    <nav aria-label="Products navigation">
                        <ul class="pagination justify-content-center">
                            <?php
                            $total_pages = ceil($total_products / $limit);
                            $filter_params = [];
                            if (!empty($filters['product_name'])) $filter_params['filter_product_name'] = $filters['product_name'];
                            if (!empty($filters['sku_model'])) $filter_params['filter_sku_model'] = $filters['sku_model'];
                            if (isset($filters['product_oc_status']) && $filters['product_oc_status'] !== '') $filter_params['filter_product_oc_status'] = $filters['product_oc_status'];

                            // Previous Page
                            if ($current_page > 1) {
                                $prev_page_params = http_build_query(array_merge($filter_params, ['module' => 'stock', 'page' => $current_page - 1, 'sort' => $sort_by]));
                                echo "<li class='page-item'><a class='page-link' href='" . BASE_URL . "index.php?{$prev_page_params}'>Previous</a></li>";
                            } else {
                                echo "<li class='page-item disabled'><span class='page-link'>Previous</span></li>";
                            }

                            // Page Numbers (simplified version)
                            for ($i = 1; $i <= $total_pages; $i++) {
                                $page_params = http_build_query(array_merge($filter_params, ['module' => 'stock', 'page' => $i, 'sort' => $sort_by]));
                                if ($i == $current_page) {
                                    echo "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
                                } else {
                                    if ($total_pages > 10 && abs($i - $current_page) > 2 && $i != 1 && $i != $total_pages && !($i > $current_page - 2 && $i < $current_page + 2) ) {
                                        if (!isset($ellipsis_shown_before) && $i < $current_page) { echo "<li class='page-item disabled'><span class='page-link'>...</span></li>"; $ellipsis_shown_before = true; }
                                        if (!isset($ellipsis_shown_after) && $i > $current_page) { echo "<li class='page-item disabled'><span class='page-link'>...</span></li>"; $ellipsis_shown_after = true; }
                                        continue;
                                    }
                                     echo "<li class='page-item'><a class='page-link' href='" . BASE_URL . "index.php?{$page_params}'>{$i}</a></li>";
                                }
                            }
                            unset($ellipsis_shown_before, $ellipsis_shown_after);

                            // Next Page
                            if ($current_page < $total_pages) {
                                $next_page_params = http_build_query(array_merge($filter_params, ['module' => 'stock', 'page' => $current_page + 1, 'sort' => $sort_by]));
                                echo "<li class='page-item'><a class='page-link' href='" . BASE_URL . "index.php?{$next_page_params}'>Next</a></li>";
                            } else {
                                echo "<li class='page-item disabled'><span class='page-link'>Next</span></li>";
                            }
                            ?>
                        </ul>
                    </nav>
                    <p class="text-center text-muted small">Showing <?php echo count($current_inventory_list); ?> of <?php echo $total_products; ?> total products (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>).</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Stock Movement History -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-1"></i>Stock Movement History (Placeholder)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Change</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock_movement_history as $log): ?>
                                <tr>
                                    <td><?php echo $log['date']; ?></td>
                                    <td><?php echo htmlspecialchars($log['product_name']); ?></td>
                                    <td><span class="badge bg-<?php echo $log['change'] > 0 ? 'success' : 'danger'; ?>"><?php echo $log['change']; ?></span></td>
                                    <td><?php echo htmlspecialchars($log['reason']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                     <div class="text-center mt-2">
                        <a href="#" class="btn btn-sm btn-outline-primary">View All Movements</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reorder Point Management -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cogs me-1"></i>Reorder Point Management (Placeholder)</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center">Functionality to manage reorder points will be available here.</p>
                    <div class="text-center">
                         <button class="btn btn-secondary btn-sm" title="Configure Reorder Points"><i class="fas fa-cog"></i> Configure Reorder Points</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Stock Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkUpdateModalLabel"><i class="fas fa-edit me-1"></i>Bulk Stock Update (Placeholder)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Form for bulk stock updates (e.g., CSV upload or manual entry grid) will be here.</p>
        <div class="mb-3">
            <label for="csvUpload" class="form-label">Upload CSV File</label>
            <input class="form-control" type="file" id="csvUpload" accept=".csv">
        </div>
        <textarea class="form-control" rows="5" placeholder="Or paste CSV data here..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Process Update</button>
      </div>
    </div>
  </div>
</div>

<script>
// Add any specific JS for stock management here if needed.
// For now, modal is handled by Bootstrap.
// DataTables could be initialized here for the inventoryTable in a later phase.
document.addEventListener('DOMContentLoaded', function () {
    // Example: Alert for placeholder buttons
    const valuationReportBtn = document.getElementById('inventoryValuationReportBtn');
    if (valuationReportBtn) { 
        valuationReportBtn.addEventListener('click', function() {
            alert('Inventory Valuation Report functionality will be implemented later.');
        });
    }
    
    const configureReorderBtn = document.querySelector('button[title="Configure Reorder Points"]'); // Keep this as is, assuming it's in a part of the page not being modified now
     if (configureReorderBtn) { 
        configureReorderBtn.addEventListener('click', function() {
            alert('Reorder Point Management functionality will be implemented later.');
        });
    }
});
</script>
