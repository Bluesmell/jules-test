<?php
// Functions for interacting with OpenCart and application-specific tables

/**
 * Get products with their descriptions.
 *
 * @param int $language_id The ID of the language for product descriptions.
 * @param int $limit Number of products to fetch.
 * @param int $offset Offset for pagination.
 * @return array|false Array of products or false on failure.
 */
function get_products($language_id = 1, $limit = 10, $offset = 0) {
    $prefix = get_opencart_prefix(); // Use the new function
    $sql = "SELECT p.*, pd.name, pd.description
            FROM {$prefix}product p
            LEFT JOIN {$prefix}product_description pd ON (p.product_id = pd.product_id)
            WHERE pd.language_id = ?
            ORDER BY p.product_id DESC
            LIMIT ? OFFSET ?";
    
    // Ensure language_id, limit, and offset are integers
    $language_id = (int)$language_id;
    $limit = (int)$limit;
    $offset = (int)$offset;

    return execute_query($sql, [
        ['i', $language_id],
        ['i', $limit],
        ['i', $offset]
    ]);
}

/**
 * Get categories with their descriptions.
 *
 * @param int $language_id The ID of the language for category descriptions.
 * @param int $parent_id Optional parent category ID to fetch subcategories.
 * @return array|false Array of categories or false on failure.
 */
function get_categories($language_id = 1, $parent_id = 0) {
    $prefix = get_opencart_prefix(); // Use the new function
    $sql = "SELECT c.*, cd.name, cd.description
            FROM {$prefix}category c
            LEFT JOIN {$prefix}category_description cd ON (c.category_id = cd.category_id)
            WHERE cd.language_id = ? AND c.parent_id = ?
            ORDER BY c.sort_order, cd.name ASC";
    
    $language_id = (int)$language_id;
    $parent_id = (int)$parent_id;

    return execute_query($sql, [
        ['i', $language_id],
        ['i', $parent_id]
    ]);
}

/**
 * Get a single product by its ID.
 *
 * @param int $product_id The ID of the product.
 * @param int $language_id The ID of the language for product description.
 * @return array|false|null Product data as assoc array, null if not found, false on error.
 */
function get_product_by_id($product_id, $language_id = 1) {
    $prefix = get_opencart_prefix(); // Use the new function
    $sql = "SELECT p.*, pd.name, pd.description
            FROM {$prefix}product p
            LEFT JOIN {$prefix}product_description pd ON (p.product_id = pd.product_id)
            WHERE p.product_id = ? AND pd.language_id = ?";
    
    $product_id = (int)$product_id;
    $language_id = (int)$language_id;

    $result = execute_query($sql, [
        ['i', $product_id],
        ['i', $language_id]
    ]);

    if ($result === false) return false; // Query error
    return !empty($result) ? $result[0] : null; // Return first row or null
}

/**
 * Get a single category by its ID.
 *
 * @param int $category_id The ID of the category.
 * @param int $language_id The ID of the language for category description.
 * @return array|false|null Category data as assoc array, null if not found, false on error.
 */
function get_category_by_id($category_id, $language_id = 1) {
    $prefix = get_opencart_prefix(); // Use the new function
    $sql = "SELECT c.*, cd.name, cd.description
            FROM {$prefix}category c
            LEFT JOIN {$prefix}category_description cd ON (c.category_id = cd.category_id)
            WHERE c.category_id = ? AND cd.language_id = ?";

    $category_id = (int)$category_id;
    $language_id = (int)$language_id;
    
    $result = execute_query($sql, [
        ['i', $category_id],
        ['i', $language_id]
    ]);
    
    if ($result === false) return false; // Query error
    return !empty($result) ? $result[0] : null; // Return first row or null
}

// Add more functions here as needed for other OpenCart tables (customers, orders, etc.)
// and for application-specific tables.

/**
 * Get total sales revenue and order count for a given period.
 *
 * @param string $period 'daily', 'weekly', 'monthly', or 'yearly'.
 * @return array Associative array with 'total_revenue' and 'order_count', or false on error.
 */
function get_sales_summary($period) {
    $prefix = get_opencart_prefix();
    $date_condition = '';
    $current_date = date('Y-m-d');

    switch ($period) {
        case 'daily':
            $date_condition = "DATE(o.date_added) = '{$current_date}'";
            break;
        case 'weekly':
            // Assuming week starts on Monday based on MySQL WEEKDAY function
            $date_condition = "YEARWEEK(o.date_added, 1) = YEARWEEK('{$current_date}', 1)";
            break;
        case 'monthly':
            $date_condition = "YEAR(o.date_added) = YEAR('{$current_date}') AND MONTH(o.date_added) = MONTH('{$current_date}')";
            break;
        case 'yearly':
            $date_condition = "YEAR(o.date_added) = YEAR('{$current_date}')";
            break;
        default:
            return ['total_revenue' => 0, 'order_count' => 0]; // Or handle as an error
    }

    // Consider only orders with order_status_id > 0 (i.e., not pending, cancelled, failed etc.)
    // and where total > 0 for revenue. This is a simplification.
    // A more robust solution would involve checking specific order_status_ids that represent a sale.
    $sql = "SELECT SUM(o.total) as total_revenue, COUNT(o.order_id) as order_count
            FROM `{$prefix}order` o
            WHERE {$date_condition}
              AND o.order_status_id > 0 
              AND o.total > 0"; // Only count orders that have a positive total towards revenue

    $result = execute_query($sql);

    if ($result === false) {
        return ['total_revenue' => 0, 'order_count' => 0, 'error' => 'Query failed'];
    }
    
    // execute_query returns an array of rows for SELECT
    if (!empty($result)) {
        return [
            'total_revenue' => $result[0]['total_revenue'] ?? 0,
            'order_count' => $result[0]['order_count'] ?? 0
        ];
    } else {
        return ['total_revenue' => 0, 'order_count' => 0];
    }
}

/**
 * Get top-selling products.
 *
 * @param int $limit Number of top products to fetch.
 * @param int $language_id Language ID for product names.
 * @param array $valid_order_status_ids Array of order status IDs to consider as valid sales. If empty, uses order_status_id > 0.
 * @return array Array of top-selling products with name, quantity, and total revenue, or false on error.
 */
function get_top_selling_products($limit = 5, $language_id = 1, $valid_order_status_ids = []) {
    $prefix = get_opencart_prefix();
    $limit = (int)$limit;
    $language_id = (int)$language_id;

    $order_status_condition = "o.order_status_id > 0"; // Default broad condition
    if (!empty($valid_order_status_ids)) {
        $status_ids_string = implode(',', array_map('intval', $valid_order_status_ids));
        if (!empty($status_ids_string)) {
            $order_status_condition = "o.order_status_id IN ({$status_ids_string})";
        }
    }

    // Sum quantity from oc_order_product
    // Get product name from oc_product_description
    // Join with oc_order to filter by valid sales statuses
    $sql = "SELECT pd.name, SUM(op.quantity) as total_quantity_sold, SUM(op.total + op.tax) as total_revenue
            FROM `{$prefix}order_product` op
            JOIN `{$prefix}product_description` pd ON op.product_id = pd.product_id
            JOIN `{$prefix}order` o ON op.order_id = o.order_id
            WHERE pd.language_id = ? AND {$order_status_condition}
            GROUP BY op.product_id, pd.name
            ORDER BY total_quantity_sold DESC
            LIMIT ?";
    
    $results = execute_query($sql, [
        ['i', $language_id],
        ['i', $limit]
    ]);

    if ($results === false) {
        return []; // Return empty array on error or no results to prevent issues in frontend
    }
    return $results;
}

/**
 * Get order count breakdown by order status.
 *
 * @param int $language_id Language ID for order status names.
 * @return array Array of order statuses with name and count, or false on error.
 */
function get_order_status_breakdown($language_id = 1) {
    $prefix = get_opencart_prefix();
    $language_id = (int)$language_id;

    $sql = "SELECT os.name, COUNT(o.order_id) as order_count
            FROM `{$prefix}order` o
            JOIN `{$prefix}order_status` os ON o.order_status_id = os.order_status_id
            WHERE os.language_id = ?
            GROUP BY o.order_status_id, os.name
            ORDER BY order_count DESC";
    
    $results = execute_query($sql, [['i', $language_id]]);

    if ($results === false) {
        return []; // Return empty array on error or no results
    }
    return $results;
}

/**
 * Get monthly revenue totals for a specified number of past months.
 *
 * @param int $num_months Number of past months to retrieve data for (including current month).
 * @param array $valid_order_status_ids Array of order status IDs to consider as valid sales. If empty, uses order_status_id > 0.
 * @return array Array of months with 'month_year' (e.g., 'YYYY-MM') and 'total_revenue', or empty array on error.
 */
function get_monthly_revenue_for_chart($num_months = 6, $valid_order_status_ids = []) {
    $prefix = get_opencart_prefix();
    $num_months = (int)$num_months;
    if ($num_months <= 0) $num_months = 6;

    $order_status_condition = "o.order_status_id > 0"; // Default broad condition
    if (!empty($valid_order_status_ids)) {
        $status_ids_string = implode(',', array_map('intval', $valid_order_status_ids));
        if (!empty($status_ids_string)) {
            $order_status_condition = "o.order_status_id IN ({$status_ids_string})";
        }
    }

    // Calculate the date N months ago from the first day of the current month
    // to ensure full months are included in the period.
    $start_date = date('Y-m-01', strtotime("-".($num_months - 1)." months"));

    $sql = "SELECT DATE_FORMAT(o.date_added, '%Y-%m') as month_year, SUM(o.total) as monthly_revenue
            FROM `{$prefix}order` o
            WHERE o.date_added >= ? 
              AND {$order_status_condition}
              AND o.total > 0
            GROUP BY month_year
            ORDER BY month_year ASC
            LIMIT ?"; // Limit in case of very sparse data over many months, though date range is primary filter.
                    // The actual number of results will depend on data density.
    
    // The LIMIT here should ideally match num_months if we expect one row per month.
    // However, if some months have no sales, they won't appear.
    // The PHP side will need to fill in missing months with 0 revenue.

    $results = execute_query($sql, [
        ['s', $start_date],
        ['i', $num_months] // This limit is a bit arbitrary here, date range is key
    ]);

    if ($results === false) {
        return [];
    }
    
    // Post-process to ensure all months in the range are present, filling missing ones with 0
    $processed_revenue_data = [];
    $current_iteration_date = $start_date; // Initialize with the actual start date for iteration
    $end_iteration_date = date('Y-m-d'); // Today

    // Create a map of fetched results for easy lookup
    $revenue_map = [];
    foreach ($results as $row) {
        $revenue_map[$row['month_year']] = $row['monthly_revenue'];
    }

    for ($i = 0; $i < $num_months; $i++) {
        $month_key = date('Y-m', strtotime($current_iteration_date));
        $processed_revenue_data[] = [
            'month_year_label' => date('M Y', strtotime($current_iteration_date)), // e.g., "Jan 2023"
            'month_year_key' => $month_key,
            'total_revenue' => isset($revenue_map[$month_key]) ? (float)$revenue_map[$month_key] : 0.00
        ];
        // Move to the first day of the next month
        $current_iteration_date = date('Y-m-01', strtotime($current_iteration_date . ' +1 month'));
        // Stop if we somehow exceed current month significantly (safety for loop logic)
        // This safety break might be too aggressive if $start_date is far in the past and $num_months is large.
        // The array_slice at the end is a more robust way to ensure correct length.
        if (strtotime($current_iteration_date) > strtotime(date('Y-m-01', strtotime($end_iteration_date . ' +2 month')))) { 
            // Allow one month beyond current to ensure current month is processed if $start_date was current month
            break;
        }
    }
    
    // Ensure we don't have more than num_months due to loop conditions or if data spans more months than requested
    return array_slice($processed_revenue_data, 0, $num_months);
}

/**
 * Get a list of orders with basic details.
 *
 * @param array $filters Associative array of filters (e.g., ['order_status_id' => 5]).
 * @param string $sort_by Sort criteria (e.g., 'date_desc', 'total_asc').
 * @param int $limit Number of orders to fetch.
 * @param int $offset Offset for pagination.
 * @param int $language_id Language ID for status names.
 * @return array Array of orders, or empty array on failure/no results.
 */
function get_orders_list($filters = [], $sort_by = 'date_desc', $limit = 20, $offset = 0, $language_id = 1) {
    $prefix = get_opencart_prefix();
    $limit = (int)$limit;
    $offset = (int)$offset;
    $language_id = (int)$language_id;
    $params = [];

    $sql_select = "SELECT o.order_id, 
                          IF(o.customer_id > 0, CONCAT(c.firstname, ' ', c.lastname), CONCAT(o.firstname, ' ', o.lastname)) as customer_name,
                          os.name as status_name, o.total, o.currency_code, o.date_added";
    $sql_from = " FROM `{$prefix}order` o
                  LEFT JOIN `{$prefix}order_status` os ON o.order_status_id = os.order_status_id AND os.language_id = ?
                  LEFT JOIN `{$prefix}customer` c ON o.customer_id = c.customer_id";
    $sql_where = " WHERE 1=1"; // Start WHERE clause

    $params[] = ['i', $language_id]; // For os.language_id

    // Basic filtering (to be expanded)
    if (!empty($filters['order_status_id'])) {
        $sql_where .= " AND o.order_status_id = ?";
        $params[] = ['i', (int)$filters['order_status_id']];
    }
    if (!empty($filters['customer_name'])) {
        // This is a simple name search, can be improved with full-text or separate firstname/lastname search
        $sql_where .= " AND (CONCAT(c.firstname, ' ', c.lastname) LIKE ? OR CONCAT(o.firstname, ' ', o.lastname) LIKE ?)";
        $name_like = '%' . $filters['customer_name'] . '%';
        $params[] = ['s', $name_like];
        $params[] = ['s', $name_like];
    }
     if (!empty($filters['order_id'])) {
        $sql_where .= " AND o.order_id = ?";
        $params[] = ['i', (int)$filters['order_id']];
    }
    // Add more filters as needed (date range, total amount range, etc.)

    // Sorting
    $sql_order_by = " ORDER BY ";
    switch ($sort_by) {
        case 'date_asc':
            $sql_order_by .= "o.date_added ASC";
            break;
        case 'total_desc':
            $sql_order_by .= "o.total DESC";
            break;
        case 'total_asc':
            $sql_order_by .= "o.total ASC";
            break;
        case 'customer_asc': // Sort by derived customer_name
            $sql_order_by .= "customer_name ASC"; // Note: some DBs might not allow alias in ORDER BY directly
            break;
        case 'customer_desc':
            $sql_order_by .= "customer_name DESC";
            break;
        default: // 'date_desc'
            $sql_order_by .= "o.date_added DESC";
    }

    $sql_limit = " LIMIT ? OFFSET ?";
    $params[] = ['i', $limit];
    $params[] = ['i', $offset];

    $full_sql = $sql_select . $sql_from . $sql_where . $sql_order_by . $sql_limit;
    
    $results = execute_query($full_sql, $params);
    return ($results === false) ? [] : $results;
}

/**
 * Get the total count of orders, optionally applying filters.
 *
 * @param array $filters Associative array of filters (same as get_orders_list).
 * @return int Total number of orders, or 0 on failure.
 */
function get_total_orders_count($filters = []) {
    $prefix = get_opencart_prefix();
    $params = [];
    // Reconstruct WHERE clause similar to get_orders_list for accurate counting
    // but without language_id in WHERE as it's for JOIN condition in main query.
    // Customer join is needed if filtering by customer name.
    $sql_from = " FROM `{$prefix}order` o 
                  LEFT JOIN `{$prefix}customer` c ON o.customer_id = c.customer_id";
    $sql_where = " WHERE 1=1";

    if (!empty($filters['order_status_id'])) {
        $sql_where .= " AND o.order_status_id = ?";
        $params[] = ['i', (int)$filters['order_status_id']];
    }
    if (!empty($filters['customer_name'])) {
        $sql_where .= " AND (CONCAT(c.firstname, ' ', c.lastname) LIKE ? OR CONCAT(o.firstname, ' ', o.lastname) LIKE ?)";
        $name_like = '%' . $filters['customer_name'] . '%';
        $params[] = ['s', $name_like];
        $params[] = ['s', $name_like];
    }
    if (!empty($filters['order_id'])) {
        $sql_where .= " AND o.order_id = ?";
        $params[] = ['i', (int)$filters['order_id']];
    }

    $full_sql = "SELECT COUNT(o.order_id) as total_count" . $sql_from . $sql_where;
    
    $result = execute_query($full_sql, $params);
    
    if ($result === false || empty($result)) {
        return 0;
    }
    return (int)$result[0]['total_count'];
}

/**
 * Get a list of products with their inventory levels.
 *
 * @param array $filters Associative array of filters (e.g., ['product_name' => 'T-Shirt']).
 * @param string $sort_by Sort criteria (e.g., 'name_asc', 'quantity_desc').
 * @param int $limit Number of products to fetch.
 * @param int $offset Offset for pagination.
 * @param int $language_id Language ID for product names.
 * @return array Array of products, or empty array on failure/no results.
 */
function get_inventory_levels($filters = [], $sort_by = 'name_asc', $limit = 20, $offset = 0, $language_id = 1) {
    $prefix = get_opencart_prefix();
    $limit = (int)$limit;
    $offset = (int)$offset;
    $language_id = (int)$language_id;
    $params = [];

    $sql_select = "SELECT p.product_id, pd.name as product_name, p.model, p.sku, p.quantity, p.status as product_oc_status"; // p.status is OC product status
    $sql_from = " FROM `{$prefix}product` p
                  LEFT JOIN `{$prefix}product_description` pd ON p.product_id = pd.product_id AND pd.language_id = ?";
    $sql_where = " WHERE 1=1";
    $params[] = ['i', $language_id];

    // Basic filtering (to be expanded)
    if (!empty($filters['product_name'])) {
        $sql_where .= " AND pd.name LIKE ?";
        $params[] = ['s', '%' . $filters['product_name'] . '%'];
    }
    if (!empty($filters['sku_model'])) { // Search in both SKU and Model
        $sql_where .= " AND (p.sku LIKE ? OR p.model LIKE ?)";
        $sku_like = '%' . $filters['sku_model'] . '%';
        $params[] = ['s', $sku_like];
        $params[] = ['s', $sku_like];
    }
    if (isset($filters['product_oc_status']) && $filters['product_oc_status'] !== '') { // Filter by OpenCart product status
        $sql_where .= " AND p.status = ?";
        $params[] = ['i', (int)$filters['product_oc_status']];
    }


    // Sorting
    $sql_order_by = " ORDER BY ";
    switch ($sort_by) {
        case 'name_desc':
            $sql_order_by .= "pd.name DESC";
            break;
        case 'quantity_asc':
            $sql_order_by .= "p.quantity ASC";
            break;
        case 'quantity_desc':
            $sql_order_by .= "p.quantity DESC";
            break;
        case 'sku_asc': // Order by model as primary SKU like field
            $sql_order_by .= "p.model ASC, p.sku ASC";
            break;
        case 'sku_desc':
            $sql_order_by .= "p.model DESC, p.sku DESC";
            break;
        default: // 'name_asc'
            $sql_order_by .= "pd.name ASC";
    }

    $sql_limit = " LIMIT ? OFFSET ?";
    $params[] = ['i', $limit];
    $params[] = ['i', $offset];

    $full_sql = $sql_select . $sql_from . $sql_where . $sql_order_by . $sql_limit;
    
    $results = execute_query($full_sql, $params);
    return ($results === false) ? [] : $results;
}

/**
 * Get the total count of products, optionally applying filters.
 *
 * @param array $filters Associative array of filters (same as get_inventory_levels).
 * @param int $language_id Language ID needed if filtering by name.
 * @return int Total number of products, or 0 on failure.
 */
function get_total_products_count($filters = [], $language_id = 1) {
    $prefix = get_opencart_prefix();
    $params = [];
    $language_id = (int)$language_id; // Ensure language_id is int for consistency

    $sql_from = " FROM `{$prefix}product` p";
    $sql_where = " WHERE 1=1";

    // If filtering by product_name, a JOIN is needed
    if (!empty($filters['product_name'])) {
        $sql_from .= " LEFT JOIN `{$prefix}product_description` pd ON p.product_id = pd.product_id AND pd.language_id = ?";
        $params[] = ['i', $language_id]; // Add language_id to params *first* if JOIN is conditional
        $sql_where .= " AND pd.name LIKE ?";
        $params[] = ['s', '%' . $filters['product_name'] . '%'];
    } else {
        // If no name filter, no language_id needed for WHERE, but JOIN might still be there if other pd fields were filtered
    }
    
    if (!empty($filters['sku_model'])) {
        $sql_where .= " AND (p.sku LIKE ? OR p.model LIKE ?)";
        $sku_like = '%' . $filters['sku_model'] . '%';
        $params[] = ['s', $sku_like];
        $params[] = ['s', $sku_like];
    }
    if (isset($filters['product_oc_status']) && $filters['product_oc_status'] !== '') {
        $sql_where .= " AND p.status = ?";
        $params[] = ['i', (int)$filters['product_oc_status']];
    }

    $full_sql = "SELECT COUNT(DISTINCT p.product_id) as total_count" . $sql_from . $sql_where; // Use DISTINCT p.product_id if JOINs could cause duplicates
    
    $result = execute_query($full_sql, $params);
    
    if ($result === false || empty($result)) {
        return 0;
    }
    return (int)$result[0]['total_count'];
}

/**
 * Get a list of customers.
 *
 * @param array $filters Associative array of filters (e.g., ['name' => 'John Doe']).
 * @param string $sort_by Sort criteria (e.g., 'name_asc', 'date_added_desc').
 * @param int $limit Number of customers to fetch.
 * @param int $offset Offset for pagination.
 * @return array Array of customers, or empty array on failure/no results.
 */
function get_customers_list($filters = [], $sort_by = 'name_asc', $limit = 20, $offset = 0) {
    $prefix = get_opencart_prefix(); // Ensure this line is active and not commented out.
    $limit = (int)$limit;
    $offset = (int)$offset;
    $params = [];

    $sql_select = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) as name, c.email, c.telephone, c.status as customer_oc_status, c.date_added";
    $sql_from = " FROM `{$prefix}customer` c";
    $sql_where = " WHERE 1=1";

    // Basic filtering
    if (!empty($filters['name'])) {
        $sql_where .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE ?";
        $params[] = ['s', '%' . $filters['name'] . '%'];
    }
    if (!empty($filters['email'])) {
        $sql_where .= " AND c.email LIKE ?";
        $params[] = ['s', '%' . $filters['email'] . '%'];
    }
    if (isset($filters['customer_oc_status']) && $filters['customer_oc_status'] !== '') {
        $sql_where .= " AND c.status = ?";
        $params[] = ['i', (int)$filters['customer_oc_status']];
    }

    // Sorting
    $sql_order_by = " ORDER BY ";
    switch ($sort_by) {
        case 'name_desc':
            $sql_order_by .= "name DESC"; // Using alias 'name'
            break;
        case 'email_asc':
            $sql_order_by .= "c.email ASC";
            break;
        case 'email_desc':
            $sql_order_by .= "c.email DESC";
            break;
        case 'date_added_asc':
            $sql_order_by .= "c.date_added ASC";
            break;
        case 'date_added_desc':
            $sql_order_by .= "c.date_added DESC";
            break;
        default: // 'name_asc'
            $sql_order_by .= "name ASC"; // Using alias 'name'
    }

    $sql_limit = " LIMIT ? OFFSET ?";
    $params[] = ['i', $limit];
    $params[] = ['i', $offset];

    $full_sql = $sql_select . $sql_from . $sql_where . $sql_order_by . $sql_limit;
    
    $results = execute_query($full_sql, $params);
    return ($results === false) ? [] : $results;
}

/**
 * Get the total count of customers, optionally applying filters.
 *
 * @param array $filters Associative array of filters (same as get_customers_list).
 * @return int Total number of customers, or 0 on failure.
 */
function get_total_customers_count($filters = []) {
    $prefix = get_opencart_prefix(); // Ensure this line is active and not commented out.
    $params = [];

    $sql_from = " FROM `{$prefix}customer` c";
    $sql_where = " WHERE 1=1";

    if (!empty($filters['name'])) {
        $sql_where .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE ?";
        $params[] = ['s', '%' . $filters['name'] . '%'];
    }
    if (!empty($filters['email'])) {
        $sql_where .= " AND c.email LIKE ?";
        $params[] = ['s', '%' . $filters['email'] . '%'];
    }
    if (isset($filters['customer_oc_status']) && $filters['customer_oc_status'] !== '') {
        $sql_where .= " AND c.status = ?";
        $params[] = ['i', (int)$filters['customer_oc_status']];
    }

    $full_sql = "SELECT COUNT(c.customer_id) as total_count" . $sql_from . $sql_where;
    
    $result = execute_query($full_sql, $params);
    
    if ($result === false || empty($result)) {
        return 0;
    }
    return (int)$result[0]['total_count'];
}

/**
 * Get overall customer analytics: total spending, total orders, and unique customer count.
 * Considers only valid orders from registered customers.
 *
 * @return array|false Associative array with 'total_spending', 'total_orders',
 *                     'total_unique_customers', or false on error.
 */
function get_overall_customer_analytics() {
    $prefix = get_opencart_prefix();
    $params = [];

    $valid_order_status_condition = "o.order_status_id > 0"; // Default
    if (defined('VALID_ORDER_STATUS_IDS') && is_array(VALID_ORDER_STATUS_IDS) && !empty(VALID_ORDER_STATUS_IDS)) {
        $status_ids_string = implode(',', array_map('intval', VALID_ORDER_STATUS_IDS));
        if (!empty($status_ids_string)) {
            $valid_order_status_condition = "o.order_status_id IN ({$status_ids_string})";
        }
    }

    // Note: For MySQL 5.7+, COUNT(DISTINCT ...) can be slow on large tables.
    // Consider if a subquery or multiple queries would be more performant if issues arise.
    $sql = "SELECT
                SUM(o.total) as total_spending,
                COUNT(o.order_id) as total_orders,
                COUNT(DISTINCT o.customer_id) as total_unique_customers
            FROM `{$prefix}order` o
            WHERE {$valid_order_status_condition}
              AND o.customer_id > 0"; // Only include registered customers

    $result = execute_query($sql, $params);

    if ($result === false) {
        // Query execution error
        return false;
    }

    if (empty($result)) {
        // No orders matching criteria, or no orders at all. Return zeros.
        return ['total_spending' => 0, 'total_orders' => 0, 'total_unique_customers' => 0];
    }

    return [
        'total_spending' => (float)($result[0]['total_spending'] ?? 0),
        'total_orders' => (int)($result[0]['total_orders'] ?? 0),
        'total_unique_customers' => (int)($result[0]['total_unique_customers'] ?? 0)
    ];
}

/**
 * Get the count of valid orders for each customer.
 *
 * @return array Array of ['customer_id' => ..., 'order_count' => ...] or empty array on failure/no data.
 */
function get_customer_order_counts() {
    $prefix = get_opencart_prefix();
    $params = [];

    $valid_order_status_condition = "o.order_status_id > 0"; // Default
    if (defined('VALID_ORDER_STATUS_IDS') && is_array(VALID_ORDER_STATUS_IDS) && !empty(VALID_ORDER_STATUS_IDS)) {
        $status_ids_string = implode(',', array_map('intval', VALID_ORDER_STATUS_IDS));
        if (!empty($status_ids_string)) {
            $valid_order_status_condition = "o.order_status_id IN ({$status_ids_string})";
        }
    }

    $sql = "SELECT
                o.customer_id,
                COUNT(o.order_id) as order_count
            FROM `{$prefix}order` o
            WHERE {$valid_order_status_condition}
              AND o.customer_id > 0
            GROUP BY o.customer_id
            ORDER BY order_count DESC"; // Ordering can be useful for some analyses

    $results = execute_query($sql, $params);

    if ($results === false) {
        return []; // Return empty array on error
    }

    // execute_query typically returns strings from DB, ensure order_count is int
    return array_map(function($row) {
        $row['customer_id'] = (int)$row['customer_id']; // Also ensure customer_id is int
        $row['order_count'] = (int)$row['order_count'];
        return $row;
    }, $results);
}
?>
