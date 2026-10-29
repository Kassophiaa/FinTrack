<?php

include 'includes/config.php';

if (isLoggedIn()) {
    redirect(dashboardUrl($_SESSION['role']));
}

require_once 'classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = ($_POST['role'] ?? '') === 'advisor' ? 'advisor' : 'user';

    $error = '';
    if ($full_name === '' || $email === '' || $username === '' || $password === '') {
        $error = "Please fill in all required fields.";
    } elseif (strlen($full_name) > 100) {
        $error = "Full name is too long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $username)) {
        $error = "Username must be 3–50 characters: letters, numbers, dots or underscores.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (strlen($phone) > 20) {
        $error = "Phone number is too long.";
    }

    $user = new User();
    if ($error === '' && $user->usernameExists($username)) {
        $error = "That username is already taken.";
    }
    if ($error === '' && $user->emailExists($email)) {
        $error = "An account with that email already exists.";
    }

    if ($error !== '') {
        echo json_encode(["status" => "error", "message" => $error]);
        exit;
    }

    if ($user->register($username, $password, $email, $full_name, $phone, $role)) {
        echo json_encode(["status" => "success", "message" => "Account created."]);
    } else {
        echo json_encode(["status" => "error", "message" => "We couldn't create your account. Please try again."]);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account · FinTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo CSS_PATH; ?>/app.css" rel="stylesheet">
</head>
<body>

<div class="auth-page">
    <?php include 'includes/auth_aside.php'; ?>

    <main class="auth-main">
        <div class="auth-card wide">
            <a class="sidebar-brand auth-mobile-brand" href="index.php"><span class="brand-mark"><i class="bi bi-wallet2"></i></span> FinTrack</a>

            <h2>Create your account</h2>
            <p class="auth-sub">It only takes a minute. Fields marked <span class="text-danger">*</span> are required.</p>

            <div id="alert" class="mb-3"></div>

            <form id="registerForm" novalidate>
                <div class="mb-3">
                    <label class="form-label">I am joining as</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" name="role" id="role_user" value="user" checked>
                            <label for="role_user"><i class="bi bi-person"></i><div><strong>Client</strong><span>Track my own finances</span></div></label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="role_advisor" value="advisor">
                            <label for="role_advisor"><i class="bi bi-briefcase"></i><div><strong>Advisor</strong><span>Help clients with advice</span></div></label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="full_name">Full name<span class="req">*</span></label>
                    <input type="text" class="form-control" id="full_name" placeholder="e.g. Juan Dela Cruz" autocomplete="name" maxlength="100" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-7">
                        <label class="form-label" for="email">Email<span class="req">*</span></label>
                        <input type="email" class="form-control" id="email" placeholder="you@example.com" autocomplete="email" maxlength="100" required>
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="tel" class="form-control" id="phone" placeholder="09XX XXX XXXX" autocomplete="tel" maxlength="20">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="username">Username<span class="req">*</span></label>
                    <input type="text" class="form-control" id="username" placeholder="Choose a username" autocomplete="username" maxlength="50" required>
                    <div class="form-text">Letters, numbers, dots or underscores.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Password<span class="req">*</span></label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password" placeholder="At least 6 characters" autocomplete="new-password" required>
                        <button type="button" class="toggle-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="strength-bar"><span id="strengthFill"></span></div>
                    <div class="form-text" id="strengthText">Use at least 6 characters.</div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Create account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="index.php">Sign in</a></p>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>var BASE_URL = "<?php echo BASE_URL; ?>";</script>
<script src="<?php echo JS_PATH; ?>/script.js"></script>
<script>
$(document).ready(function() {

    $("#password").on("input", function() {
        var v = $(this).val(), score = 0;
        if (v.length >= 6) score++;
        if (v.length >= 10) score++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
        if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) score++;
        var levels = [["0%", "#e3e8e5", "Use at least 6 characters."], ["30%", "#c62828", "Weak"], ["55%", "#b45309", "Fair"], ["80%", "#2d8a4e", "Good"], ["100%", "#15803d", "Strong"]];
        var l = v.length === 0 ? levels[0] : levels[Math.max(1, score)];
        $("#strengthFill").css({ width: l[0], background: l[1] });
        $("#strengthText").text(l[2]);
    });

    $("#registerForm").on("submit", function(e) {
        e.preventDefault();

        var data = {
            full_name: $("#full_name").val().trim(),
            email: $("#email").val().trim(),
            phone: $("#phone").val().trim(),
            username: $("#username").val().trim(),
            password: $("#password").val(),
            role: $("input[name=role]:checked").val()
        };

        $(".is-invalid").removeClass("is-invalid");
        var missing = ["full_name", "email", "username", "password"].filter(function(k) { return !data[k]; });
        if (missing.length) {
            missing.forEach(function(k) { $("#" + k).addClass("is-invalid"); });
            $("#" + missing[0]).focus();
            showAlert("#alert", "danger", "Please fill in all required fields.");
            return;
        }
        if (data.password.length < 6) {
            $("#password").addClass("is-invalid").focus();
            showAlert("#alert", "danger", "Password must be at least 6 characters.");
            return;
        }

        var $btn = $(this).find('button[type="submit"]');
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Creating account...');

        ajaxPost(BASE_URL + "/register.php", data, function(response) {
            if (response.status == "success") {
                window.location.href = BASE_URL + "/index.php?registered=1";
                return;
            }
            showAlert("#alert", "danger", response.message);
            $btn.prop("disabled", false).html(origText);
        }, function(msg) {
            showAlert("#alert", "danger", msg);
            $btn.prop("disabled", false).html(origText);
        });
    });
});
</script>
</body>
</html>
