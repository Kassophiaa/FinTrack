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
$recent_users = $admin->getRecentUsers(5);
$role_breakdown = $admin->getRoleBreakdown();

$title = "Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="welcome-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5>Welcome, <?php echo $_SESSION['full_name']; ?>!</h5>
                    <p>Admin Dashboard — Manage users, permissions, and view system-wide statistics.</p>
                </div>
                <div class="welcome-icon d-none d-md-block">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="dashboard-stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-people"></i></div>
                        <div class="card-title">Total Users</div>
                        <h2><?php echo $total_users; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-person-badge"></i></div>
                        <div class="card-title">Advisors</div>
                        <h2><?php echo $total_advisors; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-person"></i></div>
                        <div class="card-title">Clients</div>
                        <h2><?php echo $total_clients; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-star-fill"></i></div>
                        <div class="card-title">Avg. Rating</div>
                        <h2><?php echo $avg_rating > 0 ? $avg_rating . '/5' : '—'; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="form-card">
                    <div class="card-header"><i class="bi bi-speedometer2"></i> Quick Actions</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="manage_users.php" class="btn btn-outline-primary text-start"><i class="bi bi-people"></i> Manage Users</a>
                            <a href="permissions.php" class="btn btn-outline-primary text-start"><i class="bi bi-shield-check"></i> View Permissions</a>
                            <a href="ratings.php" class="btn btn-outline-primary text-start"><i class="bi bi-star"></i> View Ratings</a>
                            <a href="settings.php" class="btn btn-outline-primary text-start"><i class="bi bi-gear"></i> Settings</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-card">
                    <div class="card-header"><i class="bi bi-bar-chart-line"></i> User Breakdown</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-people"></i> Total Users</td>
                                <td class="text-end fw-bold"><?php echo $total_users; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-person-badge"></i> Advisors</td>
                                <td class="text-end fw-bold"><?php echo $total_advisors; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-person"></i> Clients</td>
                                <td class="text-end fw-bold"><?php echo $total_clients; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="form-card">
                    <div class="card-header"><i class="bi bi-activity"></i> Platform Activity</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-shield-check"></i> Permissions</td>
                                <td class="text-end fw-bold"><?php echo $total_permissions; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-star"></i> Ratings</td>
                                <td class="text-end fw-bold"><?php echo $total_ratings; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-journal-text"></i> Advices</td>
                                <td class="text-end fw-bold"><?php echo $total_advices; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-chat-dots"></i> Messages</td>
                                <td class="text-end fw-bold"><?php echo $total_messages; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="form-card">
                    <div class="card-header"><i class="bi bi-clock-history"></i> Recent Users</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <?php while ($u = $recent_users->fetch_assoc()): ?>
                            <tr>
                                <td style="color: var(--text-secondary);"><?php echo htmlspecialchars($u['full_name']); ?></td>
                                <td class="text-end"><span class="badge bg-secondary"><?php echo $u['role']; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
