<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once '../classes/Message.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = intval($_SESSION['user_id']);

    $message = new Message();

    if ($action == "send_message") {
        require_once '../classes/AdvisorPermission.php';
        require_once '../classes/User.php';
        $perm = new AdvisorPermission();
        $receiver_id = intval($_POST['receiver_id'] ?? 0);
        $msg = trim($_POST['message'] ?? '');

        if ($receiver_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid receiver ID"]);
            exit;
        }

        if ($receiver_id == $user_id) {
            echo json_encode(["status" => "error", "message" => "Cannot send message to yourself"]);
            exit;
        }

        if (empty($msg)) {
            echo json_encode(["status" => "error", "message" => "Message cannot be empty"]);
            exit;
        }

        if (strlen($msg) > 1000) {
            echo json_encode(["status" => "error", "message" => "Message is too long (max 1000 characters)"]);
            exit;
        }

        $receiver = new User();
        $rUser = $receiver->getUserById($receiver_id);

        if (!$rUser) {
            echo json_encode(["status" => "error", "message" => "Receiver not found"]);
            exit;
        }

        if ($rUser['role'] == 'advisor') {
            if (!$perm->canMessage($user_id, $receiver_id)) {
                echo json_encode(["status" => "error", "message" => "You need to grant messaging permission to this advisor first"]);
                exit;
            }
        } elseif ($rUser['role'] == 'user') {
            if (!$perm->canMessage($receiver_id, $user_id)) {
                echo json_encode(["status" => "error", "message" => "This client has not granted you messaging permission"]);
                exit;
            }
        }

        $result = $message->sendMessage($user_id, $receiver_id, $msg);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Message sent successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send message"]);
        }
        exit;
    }

    if ($action == "load_messages") {
        $receiver_id = intval($_POST['receiver_id'] ?? 0);

        if ($receiver_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
            exit;
        }

        $result = $message->getConversation($user_id, $receiver_id);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    if ($action == "mark_read") {
        $sender_id = intval($_POST['sender_id'] ?? 0);

        if ($sender_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid sender ID"]);
            exit;
        }

        $result = $message->markAsRead($sender_id, $user_id);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Messages marked as read"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to mark messages as read"]);
        }
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}

?>
