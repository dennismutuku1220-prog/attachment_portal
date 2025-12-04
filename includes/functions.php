<?php
/**
 * Common helper functions used across the portal.
 */

require_once __DIR__ . '/db.php';

/**
 * Escape and trim incoming form values.
 */
function sanitize_input(string $value): string
{
    return trim(strip_tags($value));
}

/**
 * Redirect helper to keep header usage consistent.
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Make sure a user is logged in.
 */
function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        redirect('/attachment_portal/login.php');
    }
}

/**
 * Restrict pages to a specific role.
 */
function require_role(string $role): void
{
    require_login();
    if ($_SESSION['role'] !== $role) {
        redirect('/attachment_portal/index.php');
    }
}

/**
 * Simple helper for displaying flash messages.
 */
function flash_message(): void
{
    if (!empty($_SESSION['flash'])) {
        echo '<div class="flash">' . $_SESSION['flash'] . '</div>';
        unset($_SESSION['flash']);
    }
}

/**
 * Store a flash message for the next request.
 */
function set_flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

/**
 * Handle uploads for PDFs / Word files with simple validation.
 */
function handle_file_upload(array $file): ?string
{
    $allowed_types = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }

    if (!in_array($file['type'], $allowed_types, true)) {
        return null;
    }

    $target_dir = __DIR__ . '/../uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $safe_name = uniqid('file_', true) . '_' . basename($file['name']);
    $target_path = $target_dir . $safe_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $safe_name;
    }

    return null;
}

/**
 * Determine where to send a user after login.
 */
function route_after_login(string $role): string
{
    switch ($role) {
        case 'admin':
            return '/attachment_portal/admin/dashboard.php';
        case 'supervisor':
            return '/attachment_portal/supervisor/dashboard.php';
        default:
            return '/attachment_portal/student/dashboard.php';
    }
}

