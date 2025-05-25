<?php
// modules/stock/index.php
$page_title = "Stock Management - OpenCart Manager";

// Placeholder data for Phase 1
$current_inventory = [
    ['id' => 1, 'product_name' => 'Placeholder Product A', 'sku' => 'PH-A', 'quantity' => 100, 'status' => 'In Stock'],
    ['id' => 2, 'product_name' => 'Placeholder Product B', 'sku' => 'PH-B', 'quantity' => 10, 'status' => 'Low Stock'],
    ['id' => 3, 'product_name' => 'Placeholder Product C', 'sku' => 'PH-C', 'quantity' => 0, 'status' => 'Out of Stock'],
    ['id' => 4, 'product_name' => 'Placeholder Product D', 'sku' => 'PH-D', 'quantity' => 250, 'status' => 'In Stock'],
];

$low_stock_alerts_threshold = 15; // Example threshold

$low_stock_products = array_filter($current_inventory, function($product) use ($low_stock_alerts_threshold) {
    return $product['quantity'] > 0 && $product['quantity'] < $low_stock_alerts_threshold;
});

$stock_movement_history = [
    ['date' => '2023-10-25 10:00', 'product_name' => 'Placeholder Product B', 'change' => -5, 'reason' => 'Order #123'],
    ['date' => '2023-10-24 15:30', 'product_name' => 'Placeholder Product A', 'change' => +50, 'reason' => 'Stock In'],
    ['date' => '2023-10-23 09:00', 'product_name' => 'Placeholder Product C', 'change' => +20, 'reason' => 'Stock In'],
    ['date' => '2023-10-23 11:00', 'product_name' => 'Placeholder Product C', 'change' => -20, 'reason' => 'Order #121'],
];

?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-boxes me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal"><i class="fas fa-edit me-1"></i> Bulk Stock Update</button>
            <button class="btn btn-sm btn-outline-secondary" title="Inventory Valuation Report"><i class="fas fa-file-alt me-1"></i> Inventory Valuation Report</button>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <?php if (!empty($low_stock_products)): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts (Threshold: <?php echo $low_stock_alerts_threshold; ?>)</h5>
                <ul class="mb-0">
                    <?php foreach ($low_stock_products as $product): ?>
                        <li><?php echo htmlspecialchars($product['product_name']); ?> (SKU: <?php echo htmlspecialchars($product['sku']); ?>) - Quantity: <?php echo $product['quantity']; ?></li>
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
                <table class="table table-bordered table-striped table-hover" id="inventoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($current_inventory as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($item['status'] === 'In Stock') echo 'success';
                                        elseif ($item['status'] === 'Low Stock') echo 'warning text-dark';
                                        else echo 'danger'; 
                                    ?>"><?php echo htmlspecialchars($item['status']); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-warning" title="Adjust Stock"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    const valuationReportBtn = document.querySelector('.btn-outline-secondary[title="Inventory Valuation Report"]');
    if (valuationReportBtn) { // Check if the button exists
        valuationReportBtn.addEventListener('click', function() {
            alert('Inventory Valuation Report functionality will be implemented later.');
        });
    }
    
    const configureReorderBtn = document.querySelector('.btn-secondary[title="Configure Reorder Points"]');
     if (configureReorderBtn) { // Check if the button exists
        configureReorderBtn.addEventListener('click', function() {
            alert('Reorder Point Management functionality will be implemented later.');
        });
    }
});
</script>
