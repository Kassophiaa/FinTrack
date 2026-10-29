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
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-bell" style="color: var(--primary);"></i> Notifications</h4>
            <div>
                <?php if ($unread > 0) { ?>
                    <span class="badge" style="background: var(--danger); color: #fff; font-size: 0.85rem;"><?php echo $unread; ?> unread</span>
                <?php } ?>
                <button class="btn btn-sm" style="background: var(--accent); color: #fff; border: none;" id="btnMarkAll"><i class="bi bi-check-all"></i> Mark All as Read</button>
            </div>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-body">
                <div class="search-bar mb-0">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Search notifications...">
                        <button class="btn btn-dark" id="btnSearch"><i class="bi bi-search"></i></button>
                        <button class="btn btn-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Title</th>
                                <th style="width: 40%;">Message</th>
                                <th style="width: 15%;">Date</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;">Action</th>
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
                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <p id="viewTitle" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Message</label>
                    <p id="viewMessage" class="mb-0"></p>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Date</label>
                    <p id="viewDate" class="mb-0" style="color: var(--text-muted);"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/notification.js"></script>
