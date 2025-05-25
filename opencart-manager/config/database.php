<?php
// Database Connection
require_once 'config.php';

$db_conn = null;

function get_db_connection() {
    global $db_conn;

    if ($db_conn === null) {
        try {
            // Using MySQLi for this example as requested
            $db_conn = new mysqli(OPENCART_DB_HOST, OPENCART_DB_USER, OPENCART_DB_PASS, OPENCART_DB_NAME);

            if ($db_conn->connect_error) {
                // In a real app, log this error more gracefully
                throw new Exception("Connection failed: " . $db_conn->connect_error);
            }
            // Ensure character set is explicitly set for every connection
            if (!$db_conn->set_charset("utf8mb4")) {
                // Log error if charset setting fails
                error_log("Error loading character set utf8mb4: %s
", $db_conn->error);
            }
        } catch (Exception $e) {
            // In a real application, log this error and show a user-friendly message
            // For now, die is acceptable for initial setup.
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die("Database connection error: " . $e->getMessage());
            } else {
                die("A database error occurred. Please try again later.");
            }
        }
    }
    return $db_conn;
}

function close_db_connection() {
    global $db_conn;
    if ($db_conn !== null) {
        $db_conn->close();
        $db_conn = null;
    }
}

/**
 * Executes a prepared statement query and returns the result.
 * Handles SELECT, INSERT, UPDATE, DELETE.
 * For SELECT, returns an array of associative arrays.
 * For INSERT, returns the insert ID or true.
 * For UPDATE/DELETE, returns the number of affected rows or true.
 * Returns false on failure.
 *
 * @param string $sql The SQL query with placeholders (?).
 * @param array $params An array of parameters to bind. ['type', value] e.g., ['s', 'stringValue'] or ['i', 123]
 * @return mixed Result set for SELECT, insert ID/true for INSERT, affected rows/true for UPDATE/DELETE, or false on failure.
 */
function execute_query($sql, $params = []) {
    $conn = get_db_connection();
    if (!$conn) return false;

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error . " SQL: " . $sql);
        return false;
    }

    if (!empty($params)) {
        $types = '';
        $values = [];
        foreach ($params as $param) {
            $types .= $param[0]; // 's', 'i', 'd', 'b'
            $values[] = $param[1];
        }
        if (!empty($types)) {
            $stmt->bind_param($types, ...$values);
        }
    }

    $execute_result = $stmt->execute();

    if ($execute_result === false) {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error . " SQL: " . $sql);
        $stmt->close();
        return false;
    }

    // Determine query type
    $query_type = strtoupper(substr(trim($sql), 0, 6));

    if ($query_type === 'SELECT') {
        $result = $stmt->get_result();
        if ($result === false) {
            $stmt->close();
            return false;
        }
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();
        return $data;
    } elseif ($query_type === 'INSERT') {
        $insert_id = $stmt->insert_id;
        $stmt->close();
        return $insert_id > 0 ? $insert_id : true; // Return true if insert_id is 0 but successful
    } elseif ($query_type === 'UPDATE' || $query_type === 'DELETE') {
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $affected_rows >= 0 ? $affected_rows : true; // Return true if 0 rows affected but successful
    }

    $stmt->close();
    return true; // For other types of SQL (e.g., CREATE TABLE)
}
?>
