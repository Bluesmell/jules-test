<?php
// Security functions

/**
 * Sanitizes input to prevent XSS.
 * Basic example, consider using a library for more robust sanitization.
 * @param string $data The input data.
 * @return string Sanitized data.
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a CSRF token.
 * Stores it in the session.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validates a CSRF token.
 * @param string $token The token from the form/request.
 * @return bool True if valid, false otherwise.
 */
function validate_csrf_token($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        // Token is valid, unset it to prevent reuse (optional, depends on strategy)
        // unset($_SESSION['csrf_token']); 
        return true;
    }
    return false;
}

// Add functions for SQL injection prevention (using prepared statements is key),
// XSS protection, session management, etc.
?>
