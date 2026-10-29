<div class="sidebar-wrapper">
    <div class="sidebar-brand">
        <h6>Navigation</h6>
    </div>
    <div class="list-group">
        <a href="<?php echo CLIENT_PATH; ?>/dashboard.php" class="list-group-item list-group-item-action <?php echo ($active == 'dashboard') ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/income.php" class="list-group-item list-group-item-action <?php echo ($active == 'income') ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i> Income
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/expenses.php" class="list-group-item list-group-item-action <?php echo ($active == 'expenses') ? 'active' : ''; ?>">
            <i class="bi bi-dash-circle"></i> Expenses
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/investments.php" class="list-group-item list-group-item-action <?php echo ($active == 'investments') ? 'active' : ''; ?>">
            <i class="bi bi-graph-up-arrow"></i> Investments
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/bank_accounts.php" class="list-group-item list-group-item-action <?php echo ($active == 'bank_accounts') ? 'active' : ''; ?>">
            <i class="bi bi-bank"></i> Bank Accounts
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/advisors.php" class="list-group-item list-group-item-action <?php echo ($active == 'advisors') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Advisors
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/messages.php" class="list-group-item list-group-item-action <?php echo ($active == 'messages') ? 'active' : ''; ?>">
            <i class="bi bi-chat-dots"></i> Messages
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/notifications.php" class="list-group-item list-group-item-action <?php echo ($active == 'notifications') ? 'active' : ''; ?>">
            <i class="bi bi-bell"></i> Notifications
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/profile.php" class="list-group-item list-group-item-action <?php echo ($active == 'profile') ? 'active' : ''; ?>">
            <i class="bi bi-person"></i> Profile
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/privacy_settings.php" class="list-group-item list-group-item-action <?php echo ($active == 'privacy_settings') ? 'active' : ''; ?>">
            <i class="bi bi-shield-lock"></i> Privacy Settings
        </a>
        <a href="<?php echo CLIENT_PATH; ?>/settings.php" class="list-group-item list-group-item-action <?php echo ($active == 'settings') ? 'active' : ''; ?>">
            <i class="bi bi-gear"></i> Settings
        </a>
    </div>
</div>
