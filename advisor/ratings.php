<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "ratings";

$title = "Ratings";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Ratings &amp; reviews</h1>
        <p>What your clients say about working with you.</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="small-label mb-2">Average rating</div>
                <div class="display-5 fw-bold" id="avgRating">—</div>
                <div id="avgStars" class="fs-5 my-1"></div>
                <div class="text-muted small" id="ratingCount"></div>
            </div>
            <div class="card-body border-top" id="distribution"></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reviews</h3>
                <div class="search-input" style="max-width: 300px;">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchKeyword" placeholder="Search client or comment…">
                    <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
                </div>
            </div>
            <div id="ratingList"></div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/rating.js"></script>
