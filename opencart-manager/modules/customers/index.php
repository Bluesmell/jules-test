<?php
// modules/customers/index.php
$page_title = "Customer Management - OpenCart Manager";

// Placeholder data for Phase 1
$customers = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'phone' => '555-1234', 'total_orders' => 5, 'total_spent' => '250.00', 'date_added' => '2023-01-15'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'phone' => '555-5678', 'total_orders' => 2, 'total_spent' => '120.50', 'date_added' => '2023-02-20'],
    ['id' => 3, 'name' => 'Robert Brown', 'email' => 'robert.brown@example.com', 'phone' => '555-8765', 'total_orders' => 10, 'total_spent' => '750.75', 'date_added' => '2022-11-05'],
    ['id' => 4, 'name' => 'Emily White', 'email' => 'emily.white@example.com', 'phone' => '555-4321', 'total_orders' => 1, 'total_spent' => '45.00', 'date_added' => '2023-05-10'],
];

$customer_segments = ['New', 'Regular', 'VIP', 'Lapsed'];
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
            <form class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="customerSearch" class="form-label">Search Customer</label>
                    <input type="text" class="form-control form-control-sm" id="customerSearch" placeholder="Name, email, or phone...">
                </div>
                <div class="col-md-3">
                    <label for="customerSegment" class="form-label">Customer Segment</label>
                    <select id="customerSegment" class="form-select form-select-sm">
                        <option selected value="">All Segments</option>
                        <?php foreach ($customer_segments as $segment): ?>
                            <option value="<?php echo strtolower($segment); ?>"><?php echo $segment; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="customerSort" class="form-label">Sort By</label>
                    <select id="customerSort" class="form-select form-select-sm">
                        <option selected value="date_added_desc">Date Added (Newest)</option>
                        <option value="date_added_asc">Date Added (Oldest)</option>
                        <option value="total_orders_desc">Total Orders (Most)</option>
                        <option value="total_spent_desc">Total Spent (Highest)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-info w-100"><i class="fas fa-search me-1"></i>Filter</button>
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="customerTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>Total Spent ($)</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo $customer['id']; ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td class="text-center"><?php echo $customer['total_orders']; ?></td>
                                <td class="text-end"><?php echo $customer['total_spent']; ?></td>
                                <td><?php echo $customer['date_added']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="View Details" onclick="alert('View details for customer ID <?php echo $customer['id']; ?> - Placeholder');"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-warning" title="Edit Customer" onclick="alert('Edit customer ID <?php echo $customer['id']; ?> - Placeholder');"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-success" title="View Purchase History" onclick="alert('View purchase history for customer ID <?php echo $customer['id']; ?> - Placeholder');"><i class="fas fa-history"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Placeholder -->
            <nav aria-label="Customer table navigation">
              <ul class="pagination justify-content-center mt-3">
                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
              </ul>
            </nav>
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
