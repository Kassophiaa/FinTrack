<?php

include 'includes/config.php';

if (isLoggedIn()) {
    $role = $_SESSION['role'];
    if ($role == "admin") {
        redirect(ADMIN_PATH . "/dashboard.php");
    } elseif ($role == "advisor") {
        redirect(ADVISOR_PATH . "/dashboard.php");
    } else {
        redirect(CLIENT_PATH . "/dashboard.php");
    }
}

require_once 'classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $user = new User();
    $result = $user->register($username, $password, $email, $full_name, $phone, $role);

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Registration successful! You may now login."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed. Username may already exist."]);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Personal Finance Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo CSS_PATH; ?>/style.css" rel="stylesheet">
    <link href="<?php echo CSS_PATH; ?>/login.css" rel="stylesheet">
    <style>body { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }</style>
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="register-card">
                <div class="card-body">
                    <div class="register-header">
                        <div class="register-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h4 class="card-title">Create Account</h4>
                        <p class="text-muted">Join FinTrack to manage your finances</p>
                    </div>

                    <div id="alert"></div>

                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your full name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone number" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">I want to register as</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">Client</option>
                                <option value="advisor">Advisor</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Choose a password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-person-plus"></i> Create Account
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0" style="font-size: 0.88rem; color: var(--text-muted);">
                        Already have an account? <a href="index.php">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var BASE_URL = "<?php echo BASE_URL; ?>";
$(document).ready(function() {
    $("#registerForm").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: BASE_URL + "/register.php",
            type: "POST",
            data: {
                full_name: $("#full_name").val(),
                email: $("#email").val(),
                phone: $("#phone").val(),
                username: $("#username").val(),
                password: $("#password").val(),
                role: $("#role").val()
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#registerForm")[0].reset();
                } else {
                    $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $("#alert").html('<div class="alert alert-danger">Connection error. Please try again.</div>');
            }
        });
    });
});
</script>
</body>
</html>
