<?php

function formatCurrency($amount) {
    return "₱" . number_format($amount, 2);
}

function formatDate($date) {
    return date("M d, Y", strtotime($date));
}

function formatDateTime($datetime) {
    return date("M d, Y h:i A", strtotime($datetime));
}

function sanitize($data) {
    return htmlspecialchars(trim($data));
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function getRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : "";
}

function generateAlert($type, $message) {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

?>
