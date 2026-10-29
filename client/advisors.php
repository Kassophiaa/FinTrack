<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "advisors";

$title = "Find Advisors";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Find a financial advisor</h1>
        <p>Browse advisors, then choose what you'd like to share to get personalized advice.</p>
    </div>
    <div class="search-input" style="max-width: 340px;">
        <i class="bi bi-search"></i>
        <input type="text" class="form-control" id="searchKeyword" placeholder="Search by name or email…">
        <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
    </div>
</div>

<div class="row g-3" id="advisorList"></div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/advisor.js"></script>
