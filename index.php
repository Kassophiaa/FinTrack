<?php

include 'includes/config.php';

if (isLoggedIn()) {
    redirect(dashboardUrl($_SESSION['role']));
}

$registered = isset($_GET['registered']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · FinTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo asset("css/app.css"); ?>" rel="stylesheet">
</head>
<body>

<div class="auth-page">
    <?php include 'includes/auth_aside.php'; ?>

    <main class="auth-main">
        <div class="auth-card">
            <a class="sidebar-brand auth-mobile-brand" href="index.php"><span class="brand-mark"><i class="bi bi-wallet2"></i></span> FinTrack</a>

            <h2>Welcome back</h2>
            <p class="auth-sub">Sign in to continue to your dashboard.</p>

            <div id="alert" class="mb-3"><?php if ($registered) { ?><div class="alert alert-success" role="alert"><i class="bi bi-check-circle"></i><span>Your account is ready. Sign in to get started.</span></div><?php } ?></div>

            <form id="loginForm" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autocomplete="username" autofocus required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                        <button type="button" class="toggle-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Sign in <i class="bi bi-arrow-right"></i></button>
            </form>

            <p class="auth-switch">New to FinTrack? <a href="register.php">Create an account</a></p>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>var BASE_URL = "<?php echo BASE_URL; ?>";</script>
<script src="<?php echo asset("js/script.js"); ?>"></script>
<script src="<?php echo asset("js/login.js"); ?>"></script>
</body>
</html>
