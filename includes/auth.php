<?php
/**
 * Authentication helper functions.
 */

require_once __DIR__ . '/functions.php';

/**
 * Attempt to log a user in.
 */
function login_user(string $email, string $password): bool
{
    global $conn;

    $query = 'SELECT * FROM users WHERE email = ? LIMIT 1';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        return true;
    }

    return false;
}

/**
 * Destroy session data and log the user out.
 */
function logout_user(): void
{
    session_unset();
    session_destroy();
}

/**
 * Fetch all supervisors for drop-down menus.
 */
function get_supervisors(): array
{
    global $conn;
    $data = [];
    $result = $conn->query("SELECT supervisors.id, users.name 
        FROM supervisors 
        JOIN users ON supervisors.user_id = users.id
        ORDER BY users.name ASC");

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

