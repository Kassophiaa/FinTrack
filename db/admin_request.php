<?php

error_reporting(E_ERROR | E_PARSE);
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once '../classes/Admin.php';
require_once '../classes/AdvisorPermission.php';
require_once '../classes/Rating.php';
require_once '../db/myDB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $admin = new Admin();

    if ($action == "display") {

        $result = $admin->getUsers();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            unset($row['password']);
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "search") {
        $keyword = trim($_POST['keyword'] ?? '');

        if (empty($keyword)) {
            $result = $admin->getUsers();
        } else {
            $result = $admin->searchUsers($keyword);
        }
        $data = [];

        while ($row = $result->fetch_assoc()) {
            unset($row['password']);
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "update_role") {
        $user_id = intval($_POST['user_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
            exit;
        }

        $allowed_roles = ['user', 'advisor', 'admin'];
        if (!in_array($role, $allowed_roles)) {
            echo json_encode(["status" => "error", "message" => "Invalid role"]);
            exit;
        }

        if ($user_id == intval($_SESSION['user_id'])) {
            echo json_encode(["status" => "error", "message" => "Cannot change your own role"]);
            exit;
        }

        $result = $admin->updateUserRole($user_id, $role);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Role updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update role"]);
        }
        exit;
    }

    if ($action == "toggle_status") {
        $user_id = intval($_POST['user_id'] ?? 0);

        if ($user_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
            exit;
        }

        if ($user_id == intval($_SESSION['user_id'])) {
            echo json_encode(["status" => "error", "message" => "Cannot change your own account status"]);
            exit;
        }

        try {
            $result = $admin->toggleUserStatus($user_id);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "User status updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update user status"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "An error occurred: " . $e->getMessage()]);
        }
        exit;
    }

    if ($action == "display_permissions") {
        $perm = new AdvisorPermission();
        $result = $perm->getAllPermissions();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "search_permissions") {
        $keyword = trim($_POST['keyword'] ?? '');
        if (empty($keyword)) {
            $perm = new AdvisorPermission();
            $result = $perm->getAllPermissions();
        } else {
            $search = "%" . $keyword . "%";
            $db = new myDB();
            $sql = "SELECT ap.*, u.full_name as client_name, a.full_name as advisor_name FROM advisor_permissions ap LEFT JOIN users u ON ap.client_id = u.user_id LEFT JOIN users a ON ap.advisor_id = a.user_id WHERE ap.status = 'Active' AND (u.full_name LIKE ? OR a.full_name LIKE ?) ORDER BY ap.permission_id DESC";
            $db->search($sql, [$search, $search], "ss");
            $result = $db->res;
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "display_all_ratings") {
        $db = new myDB();
        $sql = "SELECT r.*, u.full_name as client_name, a.full_name as advisor_name FROM ratings r LEFT JOIN users u ON r.user_id = u.user_id LEFT JOIN users a ON r.advisor_id = a.user_id WHERE r.status = 'Active' ORDER BY r.rating_id DESC";
        $db->select($sql);
        $result = $db->res;
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "search_all_ratings") {
        $keyword = trim($_POST['keyword'] ?? '');
        if (empty($keyword)) {
            $db = new myDB();
            $sql = "SELECT r.*, u.full_name as client_name, a.full_name as advisor_name FROM ratings r LEFT JOIN users u ON r.user_id = u.user_id LEFT JOIN users a ON r.advisor_id = a.user_id WHERE r.status = 'Active' ORDER BY r.rating_id DESC";
            $db->select($sql);
            $result = $db->res;
        } else {
            $search = "%" . $keyword . "%";
            $db = new myDB();
            $sql = "SELECT r.*, u.full_name as client_name, a.full_name as advisor_name FROM ratings r LEFT JOIN users u ON r.user_id = u.user_id LEFT JOIN users a ON r.advisor_id = a.user_id WHERE r.status = 'Active' AND (r.comment LIKE ? OR u.full_name LIKE ? OR a.full_name LIKE ?) ORDER BY r.rating_id DESC";
            $db->search($sql, [$search, $search, $search], "sss");
            $result = $db->res;
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "get_all_advisors") {
        $db = new myDB();
        $sql = "SELECT user_id, full_name FROM users WHERE role = 'advisor' AND status = 'Active'";
        $db->select($sql);
        $result = $db->res;
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}

?>
