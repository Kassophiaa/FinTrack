<div class="sidebar-wrapper">
    <div class="sidebar-brand">
        <h6>Navigation</h6>
    </div>
    <div class="list-group">
        <a href="<?php echo ADMIN_PATH; ?>/dashboard.php" class="list-group-item list-group-item-action <?php echo ($active == 'dashboard') ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="<?php echo ADMIN_PATH; ?>/manage_users.php" class="list-group-item list-group-item-action <?php echo ($active == 'users') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Users
        </a>
        <a href="<?php echo ADMIN_PATH; ?>/permissions.php" class="list-group-item list-group-item-action <?php echo ($active == 'permissions') ? 'active' : ''; ?>">
            <i class="bi bi-shield-lock"></i> Permissions
        </a>
        <a href="<?php echo ADMIN_PATH; ?>/ratings.php" class="list-group-item list-group-item-action <?php echo ($active == 'ratings') ? 'active' : ''; ?>">
            <i class="bi bi-star"></i> Ratings
        </a>
        <a href="<?php echo ADMIN_PATH; ?>/settings.php" class="list-group-item list-group-item-action <?php echo ($active == 'settings') ? 'active' : ''; ?>">
            <i class="bi bi-gear"></i> Settings
        </a>
    </div>
</div>
