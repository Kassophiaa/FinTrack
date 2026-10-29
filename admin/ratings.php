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
        <h1>Advisor ratings</h1>
        <p>Review client feedback and remove inappropriate ratings.</p>
    </div>
</div>

<div class="card table-card">
    <div class="card-header">
        <h3 class="card-title" id="ratingCount">Ratings</h3>
        <div class="search-input" style="max-width: 320px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search client, advisor or comment…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Advisor</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>From</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="ratingTable"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rating details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="detail-list">
                    <div><dt>Advisor</dt><dd id="modalAdvisor"></dd></div>
                    <div><dt>Client</dt><dd id="modalClient"></dd></div>
                    <div><dt>Rating</dt><dd id="modalStars"></dd></div>
                    <div><dt>Comment</dt><dd id="modalComment" style="white-space: pre-wrap;"></dd></div>
                    <div><dt>Date</dt><dd id="modalDate"></dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var URL = BASE_URL + "/db/admin_request.php";
    var rowsById = {};
    var viewModal = new bootstrap.Modal(document.getElementById("viewModal"));

    load();
    bindSearch("#searchKeyword", load);

    function load(keyword) {
        showLoading("#ratingTable", "Loading ratings...");
        ajaxPost(URL, { action: keyword ? "search_all_ratings" : "display_all_ratings", keyword: keyword || "" }, function(response) {
            var rows = response.data || [];
            rowsById = {};
            if (!keyword) $("#ratingCount").text(rows.length + " rating" + (rows.length === 1 ? "" : "s"));
            if (!rows.length) {
                $("#ratingTable").html(emptyRow(6, keyword ? "bi-search" : "bi-star", keyword ? "No matching ratings" : "No ratings yet", keyword ? "Try a different keyword." : ""));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                rowsById[row.rating_id] = row;
                html += '<tr>';
                html += '<td class="cell-title">' + escapeHtml(row.advisor_name || '—') + '</td>';
                html += '<td>' + renderStars(row.rating) + '</td>';
                html += '<td class="truncate text-secondary">' + (row.comment ? escapeHtml(row.comment) : '<span class="text-muted fst-italic">No comment</span>') + '</td>';
                html += '<td>' + escapeHtml(row.client_name || '—') + '</td>';
                html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(row.created_at)) + '</td>';
                html += '<td class="text-end"><div class="row-actions">';
                html += '<button class="btn-icon btnView" data-id="' + row.rating_id + '" title="View" aria-label="View"><i class="bi bi-eye"></i></button>';
                html += '<button class="btn-icon danger btnDelete" data-id="' + row.rating_id + '" title="Remove" aria-label="Remove"><i class="bi bi-trash"></i></button>';
                html += '</div></td></tr>';
            });
            $("#ratingTable").html(html);
        }, function(msg) {
            $("#ratingTable").html(emptyRow(6, "bi-wifi-off", "Couldn't load ratings", msg));
        });
    }

    $(document).on("click", ".btnView", function() {
        var row = rowsById[$(this).data("id")];
        $("#modalAdvisor").text(row.advisor_name || '—');
        $("#modalClient").text(row.client_name || '—');
        $("#modalStars").html(renderStars(row.rating) + ' <span class="text-muted small">' + parseInt(row.rating, 10) + ' / 5</span>');
        $("#modalComment").text(row.comment || 'No comment');
        $("#modalDate").text(formatDateTime(row.created_at));
        viewModal.show();
    });

    $(document).on("click", ".btnDelete", function() {
        var row = rowsById[$(this).data("id")];
        confirmDialog({ title: "Remove this rating?", message: "The rating from " + (row.client_name || "this client") + " for " + (row.advisor_name || "the advisor") + " will no longer count toward their average.", confirmText: "Remove" }, function() {
            ajaxPost(URL, { action: "delete_rating", rating_id: row.rating_id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") load($("#searchKeyword").val().trim());
            });
        });
    });
});
</script>
