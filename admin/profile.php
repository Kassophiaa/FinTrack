<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "profile";

$title = "Profile";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-person" style="color: var(--primary);"></i> My Profile</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-person-lines-fill"></i> Profile Information</div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" disabled>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="form-card">
            <div class="card-header"><i class="bi bi-lock"></i> Change Password</div>
            <div class="card-body">
                <form id="passwordForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-accent"><i class="bi bi-key"></i> Change Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    loadProfile();

    function loadProfile() {
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/profile_request.php",
            type: "POST",
            data: { action: "view_profile" },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    var data = response.data;
                    $("#full_name").val(data.full_name);
                    $("#email").val(data.email);
                    $("#phone").val(data.phone);
                    $("#username").val(data.username);
                    $("#role").val(data.role);
                }
            }
        });
    }

    $("#profileForm").on("submit", function(e) {
        e.preventDefault();

        var full_name = $("#full_name").val();
        var email = $("#email").val();
        var phone = $("#phone").val();

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/profile_request.php",
            type: "POST",
            data: {
                action: "update_profile",
                full_name: full_name,
                email: email,
                phone: phone
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });

    $("#passwordForm").on("submit", function(e) {
        e.preventDefault();

        var current_password = $("#current_password").val();
        var new_password = $("#new_password").val();
        var confirm_password = $("#confirm_password").val();

        if (new_password != confirm_password) {
            $("#alert").html('<div class="alert alert-danger">New password and confirmation do not match.</div>');
            return;
        }

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/profile_request.php",
            type: "POST",
            data: {
                action: "change_password",
                current_password: current_password,
                new_password: new_password
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#passwordForm")[0].reset();
                } else {
                    $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });

});
</script>
