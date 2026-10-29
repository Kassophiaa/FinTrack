<?php

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

require_once __DIR__ . '/../db/myDB.php';

$db = new myDB();
$sql = "SELECT status FROM users WHERE user_id = ?";
$db->select($sql, [$_SESSION['user_id']], "i");

$row = ($db->numRows() > 0) ? $db->res->fetch_assoc() : null;
if (!$row || $row['status'] !== 'Active') {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Each area of the app belongs to one role; send anyone else to their own dashboard.
$area_roles = ['admin' => 'admin', 'advisor' => 'advisor', 'client' => 'user'];
$area = basename(dirname($_SERVER['SCRIPT_FILENAME']));
if (isset($area_roles[$area]) && $area_roles[$area] !== $_SESSION['role']) {
    redirect(dashboardUrl($_SESSION['role']));
}

?>
