<?php

function formatCurrency($amount) {
    return "₱" . number_format((float)$amount, 2);
}

function formatDate($date) {
    return date("M d, Y", strtotime($date));
}

function formatDateTime($datetime) {
    return date("M d, Y h:i A", strtotime($datetime));
}

// URL for a local css/js file with a version stamp, so browsers re-download it whenever it changes
function asset($path) {
    $file = BASE_PATH . '/' . $path;
    $version = is_file($file) ? filemtime($file) : time();
    return BASE_URL . '/' . $path . '?v=' . $version;
}

// Escape a value for HTML output
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
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

function roleLabel($role) {
    $labels = ['admin' => 'Administrator', 'advisor' => 'Advisor', 'user' => 'Client'];
    return $labels[$role] ?? ucfirst($role);
}

function dashboardUrl($role) {
    if ($role == "admin") return ADMIN_PATH . "/dashboard.php";
    if ($role == "advisor") return ADVISOR_PATH . "/dashboard.php";
    return CLIENT_PATH . "/dashboard.php";
}

function initials($name) {
    $parts = preg_split('/\s+/', trim((string)$name));
    $out = '';
    foreach ($parts as $p) {
        if ($p !== '') $out .= mb_strtoupper(mb_substr($p, 0, 1));
        if (mb_strlen($out) >= 2) break;
    }
    return $out !== '' ? $out : '?';
}

function generateAlert($type, $message) {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

?>
