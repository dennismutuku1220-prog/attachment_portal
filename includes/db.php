<?php
/**
 * Database connection file.
 * It attempts to load `includes/config.php` (ignored) first,
 * then falls back to environment variables, then to sensible defaults.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prefer an ignored config file for credentials (create by copying config.example.php)
$config_path = __DIR__ . '/config.php';
if (file_exists($config_path)) {
    include $config_path; // should define $db_host, $db_user, $db_pass, $db_name
} else {
    // Try environment variables (useful on many hosts), then fall back to defaults.
    $db_host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
    $db_user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
    $db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'attachment_portal';
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Ensure we always store text as UTF-8.
$conn->set_charset('utf8mb4');

