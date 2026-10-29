<?php

header('Content-Type: application/json');

require_once '../includes/config.php';

require_once '../classes/User.php';
require_once '../classes/Admin.php';
require_once '../classes/Advisor.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($username) || empty($password)) {
            echo json_encode([
                "status" => "error",
                "message" => "Username and password are required"
            ]);
            exit;
        }

        $admin = new Admin();
        $result = $admin->login($username, $password);

        if ($result) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['full_name'] = $result['full_name'];
            $_SESSION['role'] = $result['role'];

            echo json_encode([
                "status" => "success",
                "role" => $result['role'],
                "redirect" => ADMIN_PATH . "/dashboard.php"
            ]);
            exit;
        }

        $advisor = new Advisor();
        $result = $advisor->login($username, $password);

        if ($result) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['full_name'] = $result['full_name'];
            $_SESSION['role'] = $result['role'];

            echo json_encode([
                "status" => "success",
                "role" => $result['role'],
                "redirect" => ADVISOR_PATH . "/dashboard.php"
            ]);
            exit;
        }

        $user = new User();
        $result = $user->login($username, $password);

        if ($result) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['full_name'] = $result['full_name'];
            $_SESSION['role'] = $result['role'];

            echo json_encode([
                "status" => "success",
                "role" => $result['role'],
                "redirect" => CLIENT_PATH . "/dashboard.php"
            ]);
            exit;
        }

        echo json_encode([
            "status" => "error",
            "message" => "Invalid username or password"
        ]);
        exit;
    }

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "A server error occurred. Please try again later."
    ]);
    exit;
}

?>
