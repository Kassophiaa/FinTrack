<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "advice";

$title = "My Advice";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Advice from your advisors</h1>
        <p>Every piece of advice you've received, newest first.</p>
    </div>
    <div class="search-input" style="max-width: 340px;">
        <i class="bi bi-search"></i>
        <input type="text" class="form-control" id="searchKeyword" placeholder="Search advice…">
        <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
    </div>
</div>

<div class="card">
    <div id="adviceList"></div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    load();
    bindSearch("#searchKeyword", load);

    function load(keyword) {
        showCardLoading("#adviceList", "Loading advice...");
        ajaxPost(BASE_URL + "/db/advice_request.php", { action: keyword ? "search_advice" : "display_advice", keyword: keyword || "" }, function(response) {
            var rows = response.data || [];
            if (!rows.length) {
                $("#adviceList").html(keyword
                    ? emptyStateHtml("bi-search", "No advice matches your search", "Try a different keyword.")
                    : emptyStateHtml("bi-journal-text", "No advice yet", "Share your data with an advisor and they can send you personalized advice.", '<a href="advisors.php" class="btn btn-primary btn-sm">Find an advisor</a>'));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                html += '<div class="list-row align-items-start py-3">';
                html += '<span class="avatar">' + escapeHtml(initials(row.advisor_name)) + '</span>';
                html += '<div class="row-main">';
                html += '<div class="d-flex justify-content-between gap-2 flex-wrap"><div class="fw-bold">' + escapeHtml(row.title) + '</div><div class="cell-sub">' + escapeHtml(formatDateTime(row.created_at)) + '</div></div>';
                html += '<div class="small text-muted mb-2">from <a href="advisor_profile.php?advisor_id=' + parseInt(row.advisor_id, 10) + '">' + escapeHtml(row.advisor_name) + '</a></div>';
                html += '<div class="text-secondary" style="white-space: pre-wrap;">' + escapeHtml(row.content) + '</div>';
                html += '</div></div>';
            });
            $("#adviceList").html(html);
        }, function(msg) {
            $("#adviceList").html(emptyStateHtml("bi-wifi-off", "Couldn't load advice", msg));
        });
    }
});
</script>
