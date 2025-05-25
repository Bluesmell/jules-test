<?php
// Authentication and Authorization functions

if (session_status() == PHP_SESSION_NONE) {
    session_name('opencart_manager_session'); // Custom session name
    session_set_cookie_params(SESSION_TIMEOUT, '/', '', isset($_SERVER['HTTPS']), true); // Secure and HttpOnly flags
    session_start();
}

/**
 * Checks if a user is logged in.
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in() {
    // This is a basic check. In a real app, you'd verify session data more thoroughly.
    return isset($_SESSION['user_id']);
}

/**
 * Logs a user in.
 * @param int $user_id The user's ID.
 * @param string $username The user's name.
 */
function login_user($user_id, $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true); 
}

/**
 * Logs a user out.
 */
function logout_user() {
    $_SESSION = array(); // Unset all session variables
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Checks if the current user has a specific role or permission.
 * Placeholder for role-based access control (RBAC).
 * @param string $role_or_permission The role or permission to check.
 * @return bool True if authorized, false otherwise.
 */
function has_permission($role_or_permission) {
    // This needs to be implemented based on your RBAC system.
    // For now, assume admin has all permissions if logged in.
    if (is_logged_in() /* && $_SESSION['user_role'] === 'admin' */) {
        return true; 
    }
    return false;
}

// Session timeout management
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    // Last activity was too long ago
    logout_user();
    // Optional: redirect to login page with a message
    // redirect(BASE_URL . 'index.php?module=auth&action=login&message=session_expired');
}
$_SESSION['last_activity'] = time(); // Update last activity time stamp

?>
