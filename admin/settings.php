<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Admin.php';

$active = "settings";

$admin = new Admin();
$total_users = $admin->getTotalUsers();

$title = "Settings";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-gear" style="color: var(--primary);"></i> Settings</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-gear"></i> Site Settings</div>
            <div class="card-body">
                <form id="settingsForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" value="Personal Finance Dashboard">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" id="currency" value="&#8369;">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="btnSaveSettings"><i class="bi bi-save"></i> Save Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header"><i class="bi bi-info-circle"></i> System Information</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-code-slash"></i> PHP Version</td>
                                <td class="text-end fw-bold"><?php echo phpversion(); ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-hdd"></i> Server</td>
                                <td class="text-end fw-bold"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-database"></i> Database</td>
                                <td class="text-end fw-bold">MySQL (mysqli)</td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-pc-display"></i> OS</td>
                                <td class="text-end fw-bold"><?php echo PHP_OS; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header"><i class="bi bi-bar-chart-line"></i> System Summary</div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-people"></i> Total Users</td>
                                <td class="text-end fw-bold"><?php echo $total_users; ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-secondary);"><i class="bi bi-calendar"></i> Server Time</td>
                                <td class="text-end fw-bold"><?php echo date('M d, Y h:i A'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    $("#btnSaveSettings").on("click", function() {
        var site_name = $("#site_name").val();
        var currency = $("#currency").val();

        $("#alert").html('<div class="alert alert-success">Settings saved successfully. (Demo only — not persisted to database)</div>');
    });

});
</script>
