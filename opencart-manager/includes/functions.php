<?php
// General helper functions

/**
 * Redirects to a given URL.
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Displays a formatted error message.
 * @param string $message The error message.
 */
function display_error($message) {
    // In a real app, this might load an error template
    echo "<div style='border: 1px solid red; padding: 10px; margin: 10px; background-color: #ffe0e0;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($message);
    echo "</div>";
}

/**
 * Loads a view/template file with optional data.
 * @param string $view_path Path to the view file relative to TEMPLATES_PATH or MODULES_PATH.
 * @param array $data Data to extract into the view's scope.
 */
function load_view($view_path, $data = []) {
    extract($data); // Extracts array keys into variables
    $full_path = TEMPLATES_PATH . '/' . $view_path; // Default to templates folder
    if (!file_exists($full_path) && defined('MODULES_PATH')) {
        // Fallback to checking inside a module's views folder if applicable (e.g. modules/dashboard/views/my_view.php)
         $full_path = MODULES_PATH . '/' . $view_path;
    }

    if (file_exists($full_path)) {
        include $full_path;
    } else {
        display_error("View not found: " . htmlspecialchars($view_path));
    }
}

/**
 * Get current language code.
 * For now, returns default. Will be expanded later.
 */
function get_current_language() {
    // Basic implementation, to be expanded with session, user preference etc.
    return DEFAULT_LANGUAGE;
}

/**
 * Get supported languages array.
 */
function get_supported_languages() {
    return unserialize(SUPPORTED_LANGUAGES);
}

// Add more helper functions as needed, e.g., for date formatting, string manipulation, etc.
?>
