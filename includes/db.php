<?php
/**
 * Database connection file.
 * Update the credentials below to match your local MySQL setup inside XAMPP.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'attachment_portal';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Ensure we always store text as UTF-8.
$conn->set_charset('utf8mb4');

