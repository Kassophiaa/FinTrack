<?php

require_once __DIR__ . '/api_auth.php';

require_once '../classes/AdvisorPermission.php';
require_once '../classes/Notification.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = intval($_SESSION['user_id']);
    $role = $_SESSION['role'];

    $perm = new AdvisorPermission();
    $notif = new Notification();

    // GRANT PERMISSION (client grants access to advisor)
    if ($action == "grant") {
        if ($role != 'user') {
            echo json_encode(["status" => "error", "message" => "Only clients can grant permissions"]);
            exit;
        }

        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        $userClass = new User();
        $advisor = $userClass->getUserById($advisor_id);
        if (!$advisor || $advisor['role'] != 'advisor') {
            echo json_encode(["status" => "error", "message" => "Invalid advisor"]);
            exit;
        }

        $view_income = intval($_POST['view_income'] ?? 0);
        $view_expenses = intval($_POST['view_expenses'] ?? 0);
        $view_investments = intval($_POST['view_investments'] ?? 0);
        $view_bank_accounts = intval($_POST['view_bank_accounts'] ?? 0);
        $can_message = intval($_POST['can_message'] ?? 0);
        $can_give_advice = intval($_POST['can_give_advice'] ?? 0);
        $can_rate = intval($_POST['can_rate'] ?? 0);

        $isNew = $perm->getPermission($user_id, $advisor_id) === null;
        $result = $perm->grantPermission($user_id, $advisor_id, $view_income, $view_expenses, $view_investments, $view_bank_accounts, $can_message, $can_give_advice, $can_rate);

        if ($result) {
            $clientName = $_SESSION['full_name'] ?? 'A client';
            if ($isNew) {
                $notif->addNotification($advisor_id, 'New client', $clientName . ' has shared their financial data with you and is looking for advice.');
            } else {
                $notif->addNotification($advisor_id, 'Permission Updated', $clientName . ' updated what financial data you can access.');
            }
            echo json_encode(["status" => "success", "message" => $isNew ? "Access shared. The advisor has been notified." : "Sharing settings updated"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to save permissions"]);
        }
        exit;
    }

    // REVOKE PERMISSION
    if ($action == "revoke") {
        if ($role != 'user') {
            echo json_encode(["status" => "error", "message" => "Only clients can revoke permissions"]);
            exit;
        }

        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        $result = $perm->revokePermission($user_id, $advisor_id);

        if ($result) {
            $notif->addNotification($advisor_id, 'Access removed', ($_SESSION['full_name'] ?? 'A client') . ' stopped sharing their financial data with you.');
            echo json_encode(["status" => "success", "message" => "Access removed. This advisor can no longer see your data."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to revoke permission"]);
        }
        exit;
    }

    // GET PERMISSION (for a specific advisor)
    if ($action == "get_permission") {
        $advisor_id = intval($_POST['advisor_id'] ?? 0);

        if ($advisor_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid advisor ID"]);
            exit;
        }

        $result = $perm->getPermission($user_id, $advisor_id);

        echo json_encode(["status" => "success", "data" => $result]);
        exit;
    }

    // GET MY PERMISSIONS (list all advisors I granted access to)
    if ($action == "my_permissions") {
        $result = $perm->getPermissionsByClient($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // ADMIN: GET ALL PERMISSIONS
    if ($action == "admin_all_permissions" && $role == 'admin') {
        $result = $perm->getAllPermissions();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // ADVISOR: GET MY PERMITTED CLIENTS
    if ($action == "permitted_clients") {
        if ($role != 'advisor') {
            echo json_encode(["status" => "error", "message" => "Access denied"]);
            exit;
        }

        $result = $perm->getPermittedClientsForAdvisor($user_id);
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
