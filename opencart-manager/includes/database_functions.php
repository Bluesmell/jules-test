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
    $prefix = OPENCART_TABLE_PREFIX;
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
    $prefix = OPENCART_TABLE_PREFIX;
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
    $prefix = OPENCART_TABLE_PREFIX;
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
    $prefix = OPENCART_TABLE_PREFIX;
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
?>
