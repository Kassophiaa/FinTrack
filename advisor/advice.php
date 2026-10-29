<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advisor.php';
require_once '../classes/AdvisorPermission.php';

$active = "advice";
$advisor = new Advisor();
$perm = new AdvisorPermission();

$title = "Advice";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-journal-text" style="color: var(--primary);"></i> Advice</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-send"></i> Send Advice to Client</div>
            <div class="card-body">
                <form id="adviceForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Client</label>
                            <select class="form-select" id="user_id" required>
                                <option value="">-- Select Client --</option>
                                <?php
                                $permittedClients = $perm->getPermittedClientsForAdvisor($_SESSION['user_id']);
                                if ($permittedClients && $permittedClients->num_rows > 0) {
                                    while ($prow = $permittedClients->fetch_assoc()) {
                                        $c = $advisor->getClientById($prow['client_id']);
                                        if ($c) {
                                            echo '<option value="' . $c['user_id'] . '">' . $c['full_name'] . ' (' . $c['username'] . ')</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" id="content" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send Advice</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/advice.js"></script>
