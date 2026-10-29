<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "clients";

$title = "My Clients";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>My clients</h1>
        <p>Clients who have shared their financial data with you.</p>
    </div>
</div>

<div class="card table-card">
    <div class="card-header">
        <h3 class="card-title" id="clientCount">Clients</h3>
        <div class="search-input" style="max-width: 340px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search name, username or email…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Shared with you</th>
                    <th>Since</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="clientTable"></tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var shareLabels = [['view_income', 'Income'], ['view_expenses', 'Expenses'], ['view_investments', 'Investments'], ['view_bank_accounts', 'Accounts']];

    load();
    bindSearch("#searchKeyword", load);

    function load(keyword) {
        showLoading("#clientTable", "Loading clients...");
        ajaxPost(BASE_URL + "/db/advisor_request.php", { action: keyword ? "search_clients" : "display_clients", keyword: keyword || "" }, function(response) {
            var rows = response.data || [];
            if (!keyword) $("#clientCount").text(rows.length + " client" + (rows.length === 1 ? "" : "s"));
            if (!rows.length) {
                $("#clientTable").html(keyword
                    ? emptyRow(4, "bi-search", "No matching clients", "Try a different keyword.")
                    : emptyRow(4, "bi-people", "No clients yet", "Clients appear here once they share access with you.", '<a href="profile.php" class="btn btn-outline-primary btn-sm">Polish your public profile</a>'));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                var shared = shareLabels.filter(function(l) { return row[l[0]] == 1; }).map(function(l) { return '<span class="pill pill-primary">' + l[1] + '</span>'; });
                html += '<tr>';
                html += '<td><div class="d-flex align-items-center gap-2"><span class="avatar sm">' + escapeHtml(initials(row.full_name)) + '</span><div><div class="cell-title">' + escapeHtml(row.full_name) + '</div><div class="cell-sub">@' + escapeHtml(row.username) + ' · ' + escapeHtml(row.email) + '</div></div></div></td>';
                html += '<td><div class="perm-tags">' + (shared.length ? shared.join("") : '<span class="text-muted small">No financial data</span>') + '</div></td>';
                html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(row.since)) + '</td>';
                html += '<td class="text-end"><div class="d-inline-flex gap-2">';
                if (row.can_message == 1) html += '<a class="btn btn-light btn-sm" href="messages.php?with=' + row.user_id + '"><i class="bi bi-chat-dots"></i></a>';
                html += '<a class="btn btn-outline-primary btn-sm" href="client_profile.php?user_id=' + row.user_id + '">Open</a>';
                html += '</div></td></tr>';
            });
            $("#clientTable").html(html);
        }, function(msg) {
            $("#clientTable").html(emptyRow(4, "bi-wifi-off", "Couldn't load clients", msg));
        });
    }
});
</script>
