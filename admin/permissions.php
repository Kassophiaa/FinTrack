<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "permissions";

$title = "Permissions";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Data-sharing permissions</h1>
        <p>Every active connection between a client and an advisor, and what's shared.</p>
    </div>
</div>

<div class="card table-card">
    <div class="card-header">
        <h3 class="card-title" id="permCount">Connections</h3>
        <div class="search-input" style="max-width: 320px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search client or advisor…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Advisor</th>
                    <th class="text-center">Income</th>
                    <th class="text-center">Expenses</th>
                    <th class="text-center">Investments</th>
                    <th class="text-center">Accounts</th>
                    <th class="text-center">Messages</th>
                    <th class="text-center">Advice</th>
                    <th class="text-center">Rating</th>
                    <th>Since</th>
                </tr>
            </thead>
            <tbody id="permTable"></tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var fields = ['view_income', 'view_expenses', 'view_investments', 'view_bank_accounts', 'can_message', 'can_give_advice', 'can_rate'];

    load();
    bindSearch("#searchKeyword", load);

    function mark(val) {
        return val == 1
            ? '<i class="bi bi-check-circle-fill text-success" title="Allowed"></i><span class="visually-hidden">Allowed</span>'
            : '<i class="bi bi-dash-circle" style="color:#c5cdc8;" title="Not allowed"></i><span class="visually-hidden">Not allowed</span>';
    }

    function load(keyword) {
        showLoading("#permTable", "Loading...");
        ajaxPost(BASE_URL + "/db/admin_request.php", { action: keyword ? "search_permissions" : "display_permissions", keyword: keyword || "" }, function(response) {
            var rows = response.data || [];
            if (!keyword) $("#permCount").text(rows.length + " active connection" + (rows.length === 1 ? "" : "s"));
            if (!rows.length) {
                $("#permTable").html(emptyRow(10, keyword ? "bi-search" : "bi-shield-check", keyword ? "No matching connections" : "No connections yet", keyword ? "Try a different name." : "Connections appear when clients share data with advisors."));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                html += '<tr><td class="cell-title">' + escapeHtml(row.client_name || '—') + '</td><td>' + escapeHtml(row.advisor_name || '—') + '</td>';
                fields.forEach(function(f) { html += '<td class="text-center">' + mark(row[f]) + '</td>'; });
                html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(row.created_at)) + '</td></tr>';
            });
            $("#permTable").html(html);
        }, function(msg) {
            $("#permTable").html(emptyRow(10, "bi-wifi-off", "Couldn't load permissions", msg));
        });
    }
});
</script>
