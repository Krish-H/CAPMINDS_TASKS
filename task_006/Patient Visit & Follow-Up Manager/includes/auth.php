<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_current_user_role() {
    return $_SESSION['role'] ?? null;
}

function is_admin() {
    return get_current_user_role() === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /Patient Visit & Follow-Up Manager/login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if (get_current_user_role() !== $role) {
        // You could redirect to a '403 Forbidden' page instead
        die("Access Denied: You do not have permission to view this page.");
    }
}
?>
