<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/AdvisorPermission.php';

$active = "advice";

$clients = [];
$res = (new AdvisorPermission())->getPermittedClientsForAdvisor($_SESSION['user_id']);
while ($res && ($row = $res->fetch_assoc())) {
    if ($row['can_give_advice'] == 1) $clients[] = $row;
}
$preselect = intval($_GET['client'] ?? 0);

$title = "Advice";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Advice</h1>
        <p>Write advice for your clients and manage what you've sent.</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-journal-plus"></i> New advice</h3></div>
            <div class="card-body">
                <?php if (!$clients) { ?>
                    <div class="empty-state py-3">
                        <div class="empty-icon"><i class="bi bi-people"></i></div>
                        <h6>No clients to advise yet</h6>
                        <p>Clients who allow you to send advice will appear here.</p>
                    </div>
                <?php } else { ?>
                <form id="adviceForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label" for="user_id">Client<span class="req">*</span></label>
                        <select class="form-select" id="user_id" required>
                            <option value="">Select a client</option>
                            <?php foreach ($clients as $c) { ?>
                                <option value="<?php echo intval($c['client_id']); ?>" <?php echo $preselect == $c['client_id'] ? 'selected' : ''; ?>><?php echo e($c['full_name']); ?> (@<?php echo e($c['username']); ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">Title<span class="req">*</span></label>
                        <input type="text" class="form-control" id="title" maxlength="100" placeholder="e.g. Reduce dining-out spending" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="content">Advice<span class="req">*</span></label>
                        <textarea class="form-control" id="content" rows="6" maxlength="2000" placeholder="Write clear, actionable recommendations…" required></textarea>
                        <div class="form-text"><span id="contentCount">0</span>/2000</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> Send advice</button>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card table-card">
            <div class="card-header">
                <h3 class="card-title">Sent advice</h3>
                <div class="search-input" style="max-width: 300px;">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchKeyword" placeholder="Search title or content…">
                    <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Advice</th>
                            <th>Client</th>
                            <th>Sent</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="advisorAdviceTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="viewTitle"></h5>
                    <div class="small text-muted">To <span id="viewClient"></span> · <span id="viewDate"></span></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><div id="viewContent" style="white-space: pre-wrap;"></div></div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" id="editForm" novalidate>
            <div class="modal-header">
                <h5 class="modal-title">Edit advice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_advice_id">
                <div class="mb-3">
                    <label class="form-label" for="edit_title">Title<span class="req">*</span></label>
                    <input type="text" class="form-control" id="edit_title" maxlength="100" required>
                </div>
                <div>
                    <label class="form-label" for="edit_content">Advice<span class="req">*</span></label>
                    <textarea class="form-control" id="edit_content" rows="8" maxlength="2000" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/advice.js"></script>
