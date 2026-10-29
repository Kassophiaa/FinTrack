<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Admin.php';

$active = "dashboard";

$admin = new Admin();
$total_users = $admin->getTotalUsers();
$total_advisors = $admin->getTotalAdvisors();
$total_clients = $admin->getTotalClients();
$total_permissions = $admin->getTotalPermissions();
$total_ratings = $admin->getTotalRatings();
$total_advices = $admin->getTotalAdvices();
$total_messages = $admin->getTotalMessages();
$avg_rating = $admin->getAverageRating();
$recent_users = $admin->getRecentUsers(6);

$role_pills = ['admin' => 'pill-danger', 'advisor' => 'pill-info', 'user' => 'pill-neutral'];

$title = "Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="hero-card">
    <div>
        <h2>Welcome, <?php echo e(explode(' ', trim($_SESSION['full_name']))[0]); ?></h2>
        <p>Here's an overview of the FinTrack platform.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="manage_users.php" class="btn btn-light"><i class="bi bi-people"></i> Manage users</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="manage_users.php">
            <div class="stat-top"><span class="stat-label">Active users</span><span class="icon-chip chip-primary"><i class="bi bi-people"></i></span></div>
            <div class="stat-value"><?php echo intval($total_users); ?></div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="manage_users.php">
            <div class="stat-top"><span class="stat-label">Clients</span><span class="icon-chip chip-success"><i class="bi bi-person"></i></span></div>
            <div class="stat-value"><?php echo intval($total_clients); ?></div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="manage_users.php">
            <div class="stat-top"><span class="stat-label">Advisors</span><span class="icon-chip chip-info"><i class="bi bi-briefcase"></i></span></div>
            <div class="stat-value"><?php echo intval($total_advisors); ?></div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="ratings.php">
            <div class="stat-top"><span class="stat-label">Average advisor rating</span><span class="icon-chip chip-warning"><i class="bi bi-star"></i></span></div>
            <div class="stat-value"><?php echo $avg_rating > 0 ? e($avg_rating) . ' <span class="fs-6 text-muted fw-semibold">/ 5</span>' : '—'; ?></div>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title">Platform activity</h3></div>
            <a class="list-row" href="permissions.php">
                <span class="icon-chip chip-primary"><i class="bi bi-shield-check"></i></span>
                <div class="row-main"><div class="row-title">Client–advisor connections</div><div class="row-sub">Active data-sharing permissions</div></div>
                <div class="fw-bold fs-5"><?php echo intval($total_permissions); ?></div>
            </a>
            <div class="list-row">
                <span class="icon-chip chip-success"><i class="bi bi-journal-text"></i></span>
                <div class="row-main"><div class="row-title">Advice given</div><div class="row-sub">Across all advisors</div></div>
                <div class="fw-bold fs-5"><?php echo intval($total_advices); ?></div>
            </div>
            <div class="list-row">
                <span class="icon-chip chip-info"><i class="bi bi-chat-dots"></i></span>
                <div class="row-main"><div class="row-title">Messages sent</div><div class="row-sub">Between clients and advisors</div></div>
                <div class="fw-bold fs-5"><?php echo intval($total_messages); ?></div>
            </div>
            <a class="list-row" href="ratings.php">
                <span class="icon-chip chip-warning"><i class="bi bi-star"></i></span>
                <div class="row-main"><div class="row-title">Ratings submitted</div><div class="row-sub">Client feedback on advisors</div></div>
                <div class="fw-bold fs-5"><?php echo intval($total_ratings); ?></div>
            </a>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Newest users</h3>
                <a href="manage_users.php" class="small fw-semibold">View all</a>
            </div>
            <?php while ($u = $recent_users->fetch_assoc()) { ?>
                <div class="list-row">
                    <span class="avatar sm"><?php echo e(initials($u['full_name'])); ?></span>
                    <div class="row-main">
                        <div class="row-title"><?php echo e($u['full_name']); ?></div>
                        <div class="row-sub">@<?php echo e($u['username']); ?> · <?php echo e($u['email']); ?></div>
                    </div>
                    <span class="pill <?php echo $role_pills[$u['role']] ?? 'pill-neutral'; ?>"><?php echo e(roleLabel($u['role'])); ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
