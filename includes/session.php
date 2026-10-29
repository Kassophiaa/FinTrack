<?php

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/../db/myDB.php';

$db = new myDB();
$sql = "SELECT status FROM users WHERE user_id = ?";
$db->select($sql, [$_SESSION['user_id']], "i");

if ($db->numRows() > 0) {
    $row = $db->res->fetch_assoc();
    if ($row['status'] !== 'Active') {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
} else {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

?>
