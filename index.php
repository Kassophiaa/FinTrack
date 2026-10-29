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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Personal Finance Dashboard</title>
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
        <div class="col-md-4">
            <div class="login-card">
                <div class="card-body">
                    <div class="login-header">
                        <div class="login-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h4 class="card-title">FinTrack</h4>
                        <p class="text-muted">Sign in to manage your finances</p>
                    </div>

                    <div id="alert"></div>

                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0" style="font-size: 0.88rem; color: var(--text-muted);">
                        Don't have an account? <a href="register.php">Create one</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>var BASE_URL = "<?php echo BASE_URL; ?>";</script>
<script src="js/script.js"></script>
<script src="js/login.js"></script>
</body>
</html>
