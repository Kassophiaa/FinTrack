<?php

// Shared guard for JSON endpoints: requires a signed-in user whose account is still active.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Your session has expired. Please sign in again."]);
    exit;
}

require_once __DIR__ . '/myDB.php';

$authDb = new myDB();
$authDb->select("SELECT status FROM users WHERE user_id = ?", [$_SESSION['user_id']], "i");
$authRow = ($authDb->numRows() > 0) ? $authDb->res->fetch_assoc() : null;

if (!$authRow || $authRow['status'] !== 'Active') {
    session_unset();
    session_destroy();
    echo json_encode(["status" => "error", "message" => "Your account is no longer active."]);
    exit;
}

?>
