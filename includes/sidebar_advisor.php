<div class="sidebar-wrapper">
    <div class="sidebar-brand">
        <h6>Navigation</h6>
    </div>
    <div class="list-group">
        <a href="<?php echo ADVISOR_PATH; ?>/dashboard.php" class="list-group-item list-group-item-action <?php echo ($active == 'dashboard') ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="<?php echo ADVISOR_PATH; ?>/clients.php" class="list-group-item list-group-item-action <?php echo ($active == 'clients') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Clients
        </a>

        <a href="<?php echo ADVISOR_PATH; ?>/ratings.php" class="list-group-item list-group-item-action <?php echo ($active == 'ratings') ? 'active' : ''; ?>">
            <i class="bi bi-star"></i> Ratings
        </a>
        <a href="<?php echo ADVISOR_PATH; ?>/messages.php" class="list-group-item list-group-item-action <?php echo ($active == 'messages') ? 'active' : ''; ?>">
            <i class="bi bi-chat-dots"></i> Messages
        </a>
        <a href="<?php echo ADVISOR_PATH; ?>/notifications.php" class="list-group-item list-group-item-action <?php echo ($active == 'notifications') ? 'active' : ''; ?>">
            <i class="bi bi-bell"></i> Notifications
        </a>
        <a href="<?php echo ADVISOR_PATH; ?>/profile.php" class="list-group-item list-group-item-action <?php echo ($active == 'profile') ? 'active' : ''; ?>">
            <i class="bi bi-person-gear"></i> Profile
        </a>
    </div>
</div>
