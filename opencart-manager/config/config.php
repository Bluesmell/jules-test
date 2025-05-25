<?php
// Site Configuration

// Base URL of the application (autodetect if possible, otherwise set manually)
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));

// Directory Paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('MODULES_PATH', ROOT_PATH . '/modules');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// OpenCart Settings (Placeholders - user will configure these)
define('OPENCART_DB_HOST', 'localhost');
define('OPENCART_DB_USER', 'db_user');
define('OPENCART_DB_PASS', 'db_password');
define('OPENCART_DB_NAME', 'opencart_db');
define('OPENCART_TABLE_PREFIX', 'oc_'); // Common OpenCart prefix

// Multi-language settings
define('DEFAULT_LANGUAGE', 'en');
$supported_languages = [
    'en' => ['name' => 'English', 'code' => 'en'],
    'es' => ['name' => 'Español', 'code' => 'es'] // Example second language
];
define('SUPPORTED_LANGUAGES', serialize($supported_languages)); // Store as serialized array

// LLM Module Configuration (can be disabled)
define('LLM_MODULE_ENABLED', true);

// Email Notification Settings (Placeholders)
define('ADMIN_EMAIL', 'admin@example.com');

// Backup and Export Paths (Placeholders - ensure these are writable)
define('BACKUP_PATH', ROOT_PATH . '/backups');
define('EXPORT_PATH', ROOT_PATH . '/exports');

// Security settings
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Debugging
define('DEBUG_MODE', true); // Set to false in production

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Default Currency Symbol
define('DEFAULT_CURRENCY_SYMBOL', '$');
?>
