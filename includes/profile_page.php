<?php
// Shared "My Profile" page for every role.

require_once __DIR__ . '/../classes/User.php';
$profile = (new User())->getUserById($_SESSION['user_id']);
$is_advisor = $_SESSION['role'] == 'advisor';

?>

<div class="page-header">
    <div>
        <h1>My profile</h1>
        <p>Manage your personal details and password.</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <span class="avatar xl mb-3" id="profileAvatar"><?php echo e(initials($profile['full_name'])); ?></span>
                <h2 class="h5 mb-1" id="profileName"><?php echo e($profile['full_name']); ?></h2>
                <div class="text-muted small mb-2">@<?php echo e($profile['username']); ?></div>
                <span class="pill pill-primary"><?php echo e(roleLabel($profile['role'])); ?></span>
            </div>
            <div class="card-body border-top">
                <dl class="detail-list">
                    <div><dt>Email</dt><dd id="profileEmail"><?php echo e($profile['email']); ?></dd></div>
                    <div><dt>Phone</dt><dd id="profilePhone"><?php echo $profile['phone'] ? e($profile['phone']) : '<span class="text-muted">Not set</span>'; ?></dd></div>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-person-lines-fill"></i> Personal details</h3></div>
            <div class="card-body">
                <form id="profileForm" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="full_name">Full name<span class="req">*</span></label>
                            <input type="text" class="form-control" id="full_name" maxlength="100" value="<?php echo e($profile['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email<span class="req">*</span></label>
                            <input type="email" class="form-control" id="email" maxlength="100" value="<?php echo e($profile['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="tel" class="form-control" id="phone" maxlength="20" value="<?php echo e($profile['phone']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo e($profile['username']); ?>" disabled>
                            <div class="form-text">Usernames can't be changed.</div>
                        </div>
                    </div>
                    <div class="mt-3"><button type="submit" class="btn btn-primary">Save changes</button></div>
                </form>
            </div>
        </div>

        <?php if ($is_advisor) { ?>
        <div class="card mb-3">
            <div class="card-header">
                <div>
                    <h3 class="card-title"><i class="bi bi-briefcase"></i> Public advisor profile</h3>
                    <p class="card-subtitle">Clients see this when browsing advisors.</p>
                </div>
            </div>
            <div class="card-body">
                <form id="bioForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label" for="bio">Bio</label>
                        <textarea class="form-control" id="bio" rows="4" maxlength="1000" placeholder="Describe your expertise and how you help clients…"><?php echo e($profile['bio']); ?></textarea>
                        <div class="form-text"><span id="bioCount">0</span>/1000</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="experience">Experience</label>
                        <textarea class="form-control" id="experience" rows="4" maxlength="1000" placeholder="Your background, certifications and years of experience…"><?php echo e($profile['experience']); ?></textarea>
                        <div class="form-text"><span id="expCount">0</span>/1000</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save advisor profile</button>
                </form>
            </div>
        </div>
        <?php } ?>

        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-key"></i> Change password</h3></div>
            <div class="card-body">
                <form id="passwordForm" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="current_password">Current password</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="current_password" autocomplete="current-password" required>
                                <button type="button" class="toggle-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="new_password">New password</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="new_password" autocomplete="new-password" required>
                                <button type="button" class="toggle-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                            </div>
                            <div class="form-text">At least 6 characters.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="confirm_password">Confirm new password</label>
                            <div class="password-field">
                                <input type="password" class="form-control" id="confirm_password" autocomplete="new-password" required>
                                <button type="button" class="toggle-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3"><button type="submit" class="btn btn-primary">Update password</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/profile.js"></script>
