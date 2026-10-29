<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Rating.php';

$active = "ratings";

$rating = new Rating();
$avg = $rating->getAverageRating($_SESSION['user_id']);

$title = "Ratings";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-star" style="color: var(--accent);"></i> Ratings</h4>
        </div>

        <div id="alert"></div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dashboard-stat-card" style="background: linear-gradient(135deg, var(--accent), #f0c040); color: #fff;">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-star-fill"></i></div>
                        <div class="card-title">Average Rating</div>
                        <h2><?php echo $avg; ?> / 5</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="search-bar">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchKeyword" placeholder="Search ratings by client name or comment...">
                <button class="btn btn-primary" id="btnSearch"><i class="bi bi-search"></i></button>
                <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>

        <div class="table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="ratingTable">
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
                <h5 class="modal-title"><i class="bi bi-star"></i> Rating Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Client:</strong> <span id="viewClient"></span></p>
                <p><strong>Rating:</strong> <span id="viewRating"></span></p>
                <p><strong>Comment:</strong> <span id="viewComment"></span></p>
                <p><strong>Date:</strong> <span id="viewDate"></span></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/rating.js"></script>
