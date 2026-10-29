<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/User.php';

$active = "profile";

$user = new User();
$profile = $user->getUserById($_SESSION['user_id']);

$title = "Profile";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-person-gear" style="color: var(--primary);"></i> My Profile</h4>
        </div>

        <div id="alert"></div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person"></i> Basic Information</div>
            <div class="card-body">
                <form id="profileForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-check-lg"></i> Update Basic Info</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi bi-journal-text"></i> Bio & Experience</div>
            <div class="card-body">
                <form id="bioForm">
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" rows="4" placeholder="Write a short bio about yourself..."><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        <div class="form-text">Tell clients about your expertise and approach. Max 1000 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experience</label>
                        <textarea class="form-control" id="experience" rows="4" placeholder="Describe your professional experience..."><?php echo htmlspecialchars($profile['experience'] ?? ''); ?></textarea>
                        <div class="form-text">Share your professional background and qualifications. Max 1000 characters.</div>
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-check-lg"></i> Update Bio & Experience</button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    $("#profileForm").on("submit", function(e) {
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        setFormLoading("#profileForm", true, "Updating...");
        $.ajax({
            url: BASE_URL + "/db/profile_request.php",
            type: "POST",
            data: {
                action: "update_profile",
                email: $("#email").val(),
                full_name: $("#full_name").val(),
                phone: $("#phone").val()
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "Connection error. Please try again.");
            },
            complete: function() {
                setFormLoading("#profileForm", false, "Update Basic Info");
            }
        });
    });

    $("#bioForm").on("submit", function(e) {
        e.preventDefault();
        setFormLoading("#bioForm", true, "Updating...");
        $.ajax({
            url: BASE_URL + "/db/profile_request.php",
            type: "POST",
            data: {
                action: "update_bio_experience",
                bio: $("#bio").val(),
                experience: $("#experience").val()
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "Connection error. Please try again.");
            },
            complete: function() {
                setFormLoading("#bioForm", false, "Update Bio & Experience");
            }
        });
    });

});
</script>
