<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "advisors";

$title = "Advisors";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-people" style="color: var(--primary);"></i> Browse Advisors</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-body">
                <div class="search-bar mb-0">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Search advisors by name or email...">
                        <button class="btn btn-dark" id="btnSearch"><i class="bi bi-search"></i></button>
                        <button class="btn btn-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3" id="advisorList">
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/advisor.js"></script>
