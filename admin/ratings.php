<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Admin.php';

$active = "ratings";

$title = "All Ratings";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-star" style="color: var(--accent);"></i> All Ratings</h4>
        </div>

        <div class="search-bar">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchKeyword" placeholder="Search ratings by client, advisor, or comment...">
                <button class="btn btn-primary" id="btnSearch"><i class="bi bi-search"></i> Search</button>
                <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-x-circle"></i> Clear</button>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header"><i class="bi bi-star"></i> All Ratings</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Advisor</th>
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
                <p><strong>Client:</strong> <span id="modalClient"></span></p>
                <p><strong>Advisor:</strong> <span id="modalAdvisor"></span></p>
                <p><strong>Rating:</strong> <span id="modalStars"></span></p>
                <p><strong>Comment:</strong></p>
                <p id="modalComment" style="background: var(--bg-body); padding: 0.75rem; border-radius: var(--radius-sm);"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    loadRatings();

    function renderStars(rating) {
        var stars = '';
        for (var i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="bi bi-star-fill" style="color: var(--accent);"></i>';
            } else {
                stars += '<i class="bi bi-star" style="color: var(--text-secondary);"></i>';
            }
        }
        return stars;
    }

    function renderRows(data) {
        var html = "";
        if (data.length > 0) {
            $.each(data, function(i, row) {
                var comment = row.comment ? row.comment.substring(0, 50) : '';
                if (row.comment && row.comment.length > 50) comment += '...';
                html += '<tr>';
                html += '<td>' + row.rating_id + '</td>';
                html += '<td><strong>' + row.client_name + '</strong></td>';
                html += '<td>' + row.advisor_name + '</td>';
                html += '<td>' + renderStars(row.rating) + '</td>';
                html += '<td>' + comment + '</td>';
                html += '<td style="color: var(--text-secondary);">' + row.created_at + '</td>';
                html += '<td><button class="btn btn-sm btn-primary btnView" data-id="' + row.rating_id + '" data-client="' + row.client_name + '" data-advisor="' + row.advisor_name + '" data-rating="' + row.rating + '" data-comment="' + (row.comment ? row.comment.replace(/"/g, '&quot;') : '') + '"><i class="bi bi-eye"></i></button></td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="7" class="text-center">No ratings found</td></tr>';
        }
        return html;
    }

    function loadRatings() {
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "display_all_ratings" },
            dataType: "json",
            success: function(response) {
                $("#ratingTable").html(renderRows(response.data));
            }
        });
    }

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val();
        if (keyword == "") {
            loadRatings();
            return;
        }
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "search_all_ratings", keyword: keyword },
            dataType: "json",
            success: function(response) {
                $("#ratingTable").html(renderRows(response.data));
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadRatings();
    });

    $(document).on("click", ".btnView", function() {
        $("#modalClient").text($(this).data("client"));
        $("#modalAdvisor").text($(this).data("advisor"));
        var rating = $(this).data("rating");
        $("#modalStars").html(renderStars(rating));
        $("#modalComment").text($(this).data("comment"));
        $("#viewModal").modal("show");
    });

});
</script>
