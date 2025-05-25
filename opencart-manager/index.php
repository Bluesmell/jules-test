<?php
session_start();

require_once 'config/config.php';
require_once 'config/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/security.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/database_functions.php'; // <-- Add this line

// Basic Routing
$module = isset($_GET['module']) ? sanitize_input($_GET['module']) : 'dashboard';
$action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'index';

// Check if user is logged in, redirect to login page if not (except for auth module)
// if (!is_logged_in() && $module !== 'auth') {
//     redirect(BASE_URL . 'index.php?module=auth&action=login');
// }


// Whitelist of allowed modules
$allowed_modules = ['dashboard', 'sales', 'stock', 'customers', 'seo', 'llm', 'auth', 'settings']; // Added auth and settings

if (in_array($module, $allowed_modules)) {
    $module_path = MODULES_PATH . '/' . $module . '/index.php'; // Convention: each module has an index.php
    if (file_exists($module_path)) {
        // Load the main layout
        include_once TEMPLATES_PATH . '/header.php';
        require_once $module_path;
        include_once TEMPLATES_PATH . '/footer.php';
    } else {
        display_error("Module not found: " . htmlspecialchars($module));
    }
} else {
    display_error("Invalid module specified: " . htmlspecialchars($module));
}

close_db_connection();
?>
