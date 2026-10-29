<?php

require_once __DIR__ . '/../classes/Notification.php';
require_once __DIR__ . '/../classes/Message.php';

$nav_role = $_SESSION['role'];
$nav_unread_notifications = (new Notification())->getUnreadCount($_SESSION['user_id']);
$nav_unread_messages = (new Message())->getUnreadCount($_SESSION['user_id']);

// Sidebar menu per role: section => [key, url, icon, label]
if ($nav_role == 'admin') {
    $nav_base = ADMIN_PATH;
    $nav_menu = [
        'Overview' => [
            ['dashboard', 'dashboard.php', 'bi-grid-1x2', 'Dashboard'],
        ],
        'Management' => [
            ['users', 'manage_users.php', 'bi-people', 'Users'],
            ['permissions', 'permissions.php', 'bi-shield-check', 'Permissions'],
            ['ratings', 'ratings.php', 'bi-star', 'Ratings'],
        ],
        'Account' => [
            ['settings', 'settings.php', 'bi-hdd-stack', 'System'],
            ['profile', 'profile.php', 'bi-person-circle', 'My Profile'],
        ],
    ];
} elseif ($nav_role == 'advisor') {
    $nav_base = ADVISOR_PATH;
    $nav_menu = [
        'Overview' => [
            ['dashboard', 'dashboard.php', 'bi-grid-1x2', 'Dashboard'],
        ],
        'Work' => [
            ['clients', 'clients.php', 'bi-people', 'My Clients'],
            ['messages', 'messages.php', 'bi-chat-dots', 'Messages', $nav_unread_messages],
            ['ratings', 'ratings.php', 'bi-star', 'Ratings'],
        ],
        'Account' => [
            ['profile', 'profile.php', 'bi-person-circle', 'My Profile'],
        ],
    ];
} else {
    $nav_base = CLIENT_PATH;
    $nav_menu = [
        'Overview' => [
            ['dashboard', 'dashboard.php', 'bi-grid-1x2', 'Dashboard'],
        ],
        'My Money' => [
            ['income', 'income.php', 'bi-arrow-down-left-circle', 'Income'],
            ['expenses', 'expenses.php', 'bi-arrow-up-right-circle', 'Expenses'],
            ['investments', 'investments.php', 'bi-graph-up-arrow', 'Investments'],
            ['bank_accounts', 'bank_accounts.php', 'bi-bank', 'Accounts'],
        ],
        'Advisors' => [
            ['advisors', 'advisors.php', 'bi-search', 'Find Advisors'],
            ['messages', 'messages.php', 'bi-chat-dots', 'Messages', $nav_unread_messages],
        ],
        'Account' => [
            ['privacy_settings', 'privacy_settings.php', 'bi-shield-lock', 'Data Sharing'],
            ['profile', 'profile.php', 'bi-person-circle', 'My Profile'],
        ],
    ];
}

$active = $active ?? '';

?>
<div class="app-shell">
    <aside class="app-sidebar" id="appSidebar">
        <a class="sidebar-brand" href="<?php echo $nav_base; ?>/dashboard.php">
            <span class="brand-mark"><i class="bi bi-wallet2"></i></span> FinTrack
        </a>
        <nav class="sidebar-nav">
            <?php foreach ($nav_menu as $section => $links) { ?>
                <div class="sidebar-section"><?php echo e($section); ?></div>
                <?php foreach ($links as $link) { ?>
                    <a href="<?php echo $nav_base . '/' . $link[1]; ?>" class="sidebar-link <?php echo ($active == $link[0]) ? 'active' : ''; ?>">
                        <i class="bi <?php echo $link[2]; ?>"></i> <?php echo e($link[3]); ?>
                        <?php if (!empty($link[4])) { ?><span class="nav-count"><?php echo intval($link[4]); ?></span><?php } ?>
                    </a>
                <?php } ?>
            <?php } ?>
        </nav>
        <div class="sidebar-user">
            <span class="avatar on-dark sm"><?php echo e(initials($_SESSION['full_name'])); ?></span>
            <div class="user-meta">
                <div class="user-name"><?php echo e($_SESSION['full_name']); ?></div>
                <div class="user-role"><?php echo e(roleLabel($nav_role)); ?></div>
            </div>
            <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-logout" title="Sign out"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-main">
        <header class="app-topbar">
            <button class="topbar-icon sidebar-toggle" id="sidebarToggle" type="button" aria-label="Open menu"><i class="bi bi-list"></i></button>
            <h1 class="topbar-title"><?php echo e($title); ?></h1>
            <div class="topbar-actions">
                <?php if ($nav_role != 'admin') { ?>
                    <a href="<?php echo $nav_base; ?>/notifications.php" class="topbar-icon" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <?php if ($nav_unread_notifications > 0) { ?><span class="dot-count"><?php echo intval($nav_unread_notifications); ?></span><?php } ?>
                    </a>
                <?php } ?>
                <div class="dropdown">
                    <button class="topbar-user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar sm"><?php echo e(initials($_SESSION['full_name'])); ?></span>
                        <span class="user-name-text"><?php echo e($_SESSION['full_name']); ?></span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header"><?php echo e(roleLabel($nav_role)); ?> account</h6></li>
                        <li><a class="dropdown-item" href="<?php echo $nav_base; ?>/profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <main class="app-content">
