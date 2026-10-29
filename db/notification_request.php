<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once '../classes/Notification.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = intval($_SESSION['user_id']);

    $notif = new Notification();

    if ($action == "display") {

        $result = $notif->getNotifications($user_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "view") {
        $notification_id = intval($_POST['notification_id'] ?? 0);

        if ($notification_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid notification ID"]);
            exit;
        }

        $result = $notif->getNotificationById($notification_id);

        if (!$result || intval($result['user_id']) != $user_id) {
            echo json_encode(["status" => "error", "message" => "Notification not found or access denied"]);
            exit;
        }

        echo json_encode(["status" => "success", "data" => $result]);
        exit;
    }

    if ($action == "mark_read") {
        $notification_id = intval($_POST['notification_id'] ?? 0);

        if ($notification_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid notification ID"]);
            exit;
        }

        $existing = $notif->getNotificationById($notification_id);

        if (!$existing || intval($existing['user_id']) != $user_id) {
            echo json_encode(["status" => "error", "message" => "Notification not found or access denied"]);
            exit;
        }

        $result = $notif->markAsRead($notification_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Notification marked as read"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to mark notification as read"]);
        }
        exit;
    }

    if ($action == "mark_all_read") {

        $result = $notif->markAllAsRead($user_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "All notifications marked as read"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to mark all as read"]);
        }
        exit;
    }

    if ($action == "delete") {
        $notification_id = intval($_POST['notification_id'] ?? 0);

        if ($notification_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid notification ID"]);
            exit;
        }

        $existing = $notif->getNotificationById($notification_id);

        if (!$existing || intval($existing['user_id']) != $user_id) {
            echo json_encode(["status" => "error", "message" => "Notification not found or access denied"]);
            exit;
        }

        $result = $notif->deleteNotification($notification_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Notification deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete notification"]);
        }
        exit;
    }

    if ($action == "unread_count") {

        $count = $notif->getUnreadCount($user_id);

        echo json_encode(["status" => "success", "count" => $count]);
        exit;
    }

    if ($action == "search") {
        $keyword = trim($_POST['keyword'] ?? '');

        if (empty($keyword)) {
            $result = $notif->getNotifications($user_id);
        } else {
            $result = $notif->searchNotifications($user_id, $keyword);
        }
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
