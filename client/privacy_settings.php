<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "privacy_settings";

$title = "Privacy Settings";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-shield-lock" style="color: var(--primary);"></i> Privacy Settings</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-key"></i> Change Password</div>
            <div class="card-body">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" placeholder="Enter current password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" placeholder="Enter new password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-key"></i> Change Password</button>
                </form>
            </div>
        </div>

        <div class="form-card">
            <div class="card-header"><i class="bi bi-info-circle"></i> Account Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin-bottom: 8px;"><strong>Username:</strong> <span style="color: var(--text-secondary);"><?php echo $_SESSION['username']; ?></span></p>
                        <p class="mb-0"><strong>Role:</strong> <span class="badge" style="background: var(--primary); color: #fff;"><?php echo ucfirst($_SESSION['role']); ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-0" style="font-size: 0.88rem;"><i class="bi bi-info-circle"></i> To delete your account, please contact the administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/profile.js"></script>
