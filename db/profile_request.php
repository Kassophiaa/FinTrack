<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once '../classes/User.php';
require_once '../db/myDB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = intval($_SESSION['user_id']);

    $user = new User();

    if ($action == "view_profile") {

        $result = $user->getUserById($user_id);

        if ($result) {
            unset($result['password']);
            echo json_encode(["status" => "success", "data" => $result]);
        } else {
            echo json_encode(["status" => "error", "message" => "Profile not found"]);
        }
        exit;
    }

    if ($action == "update_profile") {
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($email) || empty($full_name)) {
            echo json_encode(["status" => "error", "message" => "Email and full name are required"]);
            exit;
        }

        if (strlen($full_name) > 100 || strlen($email) > 100 || strlen($phone) > 20) {
            echo json_encode(["status" => "error", "message" => "Input exceeds maximum length"]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format"]);
            exit;
        }

        $result = $user->updateProfile($user_id, $email, $full_name, $phone);

        if ($result) {
            $_SESSION['full_name'] = $full_name;
            echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update profile"]);
        }
        exit;
    }

    if ($action == "change_password") {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        if (empty($current_password) || empty($new_password)) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(["status" => "error", "message" => "New password must be at least 6 characters"]);
            exit;
        }

        $profile = $user->getUserById($user_id);

        if (!$profile) {
            echo json_encode(["status" => "error", "message" => "User not found"]);
            exit;
        }

        if (password_verify($current_password, $profile['password'])) {
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            $db = new myDB();
            $sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $result = $db->update($sql, [$hashedPassword, $user_id], "si");

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Password changed successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to change password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Current password is incorrect"]);
        }
        exit;
    }

    if ($action == "update_bio_experience") {
        $bio = trim($_POST['bio'] ?? '');
        $experience = trim($_POST['experience'] ?? '');

        if (strlen($bio) > 1000 || strlen($experience) > 1000) {
            echo json_encode(["status" => "error", "message" => "Input exceeds maximum length (1000 characters)"]);
            exit;
        }

        $result = $user->updateBioExperience($user_id, $bio, $experience);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Bio and experience updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update bio and experience"]);
        }
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action"]);
    exit;
}

?>
