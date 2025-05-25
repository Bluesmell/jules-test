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
?>
