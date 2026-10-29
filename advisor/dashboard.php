<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advisor.php';
require_once '../classes/Advice.php';

$active = "dashboard";

require_once '../classes/AdvisorPermission.php';

$advisor = new Advisor();
$advice = new Advice();
$perm = new AdvisorPermission();
$permitted = $perm->getPermittedClientsForAdvisor($_SESSION['user_id']);
$total_clients = 0;
$client_list = [];

if ($permitted) {
    while ($prow = $permitted->fetch_assoc()) {
        $client = $advisor->getClientById($prow['client_id']);
        if ($client) {
            $client_list[] = $client;
        }
    }
    $total_clients = count($client_list);
}

$total_advice = $advice->getAdviceByAdvisor($_SESSION['user_id']);
$total_advice_count = 0;

if ($total_advice) {
    $total_advice_count = $total_advice->num_rows;
}

$title = "Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="welcome-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5>Welcome, <?php echo $_SESSION['full_name']; ?>!</h5>
                    <p>You have <?php echo $total_clients; ?> permitted client(s) and have given <?php echo $total_advice_count; ?> advice(s).</p>
                </div>
                <div class="welcome-icon d-none d-md-block">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dashboard-stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-people"></i></div>
                        <div class="card-title">Total Clients</div>
                        <h2><?php echo $total_clients; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-journal-text"></i></div>
                        <div class="card-title">Advice Given</div>
                        <h2><?php echo $total_advice_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-person-circle"></i></div>
                        <div class="card-title">Logged In As</div>
                        <h5><?php echo $_SESSION['full_name']; ?></h5>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
