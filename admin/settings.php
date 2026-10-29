<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Admin.php';

$active = "settings";

$admin = new Admin();
$total_users = $admin->getTotalUsers();
$role_breakdown = $admin->getRoleBreakdown();

$db_version = '—';
$conn = @new mysqli(...array_values(require '../db/db_config.php'));
if (!$conn->connect_error) {
    $db_version = $conn->server_info;
    $conn->close();
}

$title = "System";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>System</h1>
        <p>Server environment and platform summary.</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-hdd-stack"></i> Environment</h3></div>
            <div class="list-row"><div class="row-main text-secondary">Application URL</div><div class="fw-semibold text-break text-end"><?php echo e(BASE_URL); ?></div></div>
            <div class="list-row"><div class="row-main text-secondary">PHP version</div><div class="fw-semibold"><?php echo e(phpversion()); ?></div></div>
            <div class="list-row"><div class="row-main text-secondary">Web server</div><div class="fw-semibold text-end"><?php echo e($_SERVER['SERVER_SOFTWARE'] ?? '—'); ?></div></div>
            <div class="list-row"><div class="row-main text-secondary">Database server</div><div class="fw-semibold">MySQL <?php echo e($db_version); ?></div></div>
            <div class="list-row"><div class="row-main text-secondary">Operating system</div><div class="fw-semibold"><?php echo e(PHP_OS); ?></div></div>
            <div class="list-row"><div class="row-main text-secondary">Server time</div><div class="fw-semibold"><?php echo e(date('M d, Y h:i A')); ?></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-people"></i> Active accounts</h3></div>
            <div class="list-row"><span class="icon-chip chip-success"><i class="bi bi-person"></i></span><div class="row-main">Clients</div><div class="fw-bold"><?php echo intval($role_breakdown['user'] ?? 0); ?></div></div>
            <div class="list-row"><span class="icon-chip chip-info"><i class="bi bi-briefcase"></i></span><div class="row-main">Advisors</div><div class="fw-bold"><?php echo intval($role_breakdown['advisor'] ?? 0); ?></div></div>
            <div class="list-row"><span class="icon-chip chip-danger"><i class="bi bi-shield-lock"></i></span><div class="row-main">Administrators</div><div class="fw-bold"><?php echo intval($role_breakdown['admin'] ?? 0); ?></div></div>
            <div class="list-row"><span class="icon-chip chip-primary"><i class="bi bi-people"></i></span><div class="row-main fw-semibold">Total</div><div class="fw-bold"><?php echo intval($total_users); ?></div></div>
            <div class="card-footer small text-muted">Currency: Philippine Peso (₱)</div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
