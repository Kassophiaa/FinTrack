<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Notification.php';

$active = "notifications";

$notif = new Notification();
$unread = $notif->getUnreadCount($_SESSION['user_id']);

$title = "Notifications";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-bell" style="color: var(--primary);"></i> Notifications</h4>
        </div>

        <div id="alert"></div>

        <div class="search-bar">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Search notifications...">
                        <button class="btn btn-primary" id="btnSearch"><i class="bi bi-search"></i></button>
                        <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="badge bg-danger me-2" style="font-size: 0.9rem;"><?php echo $unread; ?> unread</span>
                    <button class="btn btn-accent" id="btnMarkAll"><i class="bi bi-check-all"></i> Mark All as Read</button>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Title</th>
                                <th width="40%">Message</th>
                                <th width="15%">Date</th>
                                <th width="10%">Status</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="notifTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bell"></i> Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Title:</strong> <span id="viewTitle"></span></p>
                <p><strong>Message:</strong> <span id="viewMessage"></span></p>
                <p><strong>Date:</strong> <span id="viewDate"></span></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/notification.js"></script>
