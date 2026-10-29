<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "privacy_settings";

$title = "Data Sharing";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Data sharing</h1>
        <p>See exactly which advisors can access your financial information, and change it anytime.</p>
    </div>
    <div class="page-actions">
        <a href="advisors.php" class="btn btn-light"><i class="bi bi-person-plus"></i> Connect an advisor</a>
    </div>
</div>

<div class="notice info">
    <i class="bi bi-shield-lock"></i>
    <div>Advisors only see the data you switch on. Stopping sharing takes effect immediately.</div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title"><i class="bi bi-people"></i> Advisors with access</h3></div>
    <div id="permList"></div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var PERM = BASE_URL + "/db/permission_request.php";
    var labels = [
        ['view_income', 'Income'], ['view_expenses', 'Expenses'], ['view_investments', 'Investments'],
        ['view_bank_accounts', 'Bank accounts'], ['can_message', 'Messaging'], ['can_give_advice', 'Advice'], ['can_rate', 'Rating']
    ];

    load();

    function load() {
        showCardLoading("#permList", "Loading...");
        ajaxPost(PERM, { action: "my_permissions" }, function(response) {
            var rows = response.data || [];
            if (!rows.length) {
                $("#permList").html(emptyStateHtml("bi-shield-check", "You're not sharing with anyone", "Your financial data is private. Connect with an advisor to get personalized advice.", '<a href="advisors.php" class="btn btn-primary btn-sm">Find an advisor</a>'));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                var tags = labels.map(function(l) {
                    var on = row[l[0]] == 1;
                    return '<span class="pill ' + (on ? 'pill-primary' : 'pill-neutral') + '"><i class="bi ' + (on ? 'bi-check-lg' : 'bi-lock') + '"></i> ' + l[1] + '</span>';
                }).join("");
                html += '<div class="list-row align-items-start flex-wrap py-3">';
                html += '<span class="avatar">' + escapeHtml(initials(row.advisor_name)) + '</span>';
                html += '<div class="row-main"><div class="fw-bold">' + escapeHtml(row.advisor_name) + '</div>';
                html += '<div class="cell-sub mb-2">Sharing since ' + escapeHtml(formatDate(row.created_at)) + '</div>';
                html += '<div class="perm-tags">' + tags + '</div></div>';
                html += '<div class="d-flex gap-2">';
                html += '<a class="btn btn-light btn-sm" href="advisor_profile.php?advisor_id=' + parseInt(row.advisor_id, 10) + '"><i class="bi bi-sliders"></i> Manage</a>';
                html += '<button class="btn btn-outline-danger btn-sm btnRevoke" data-id="' + parseInt(row.advisor_id, 10) + '" data-name="' + escapeHtml(row.advisor_name) + '"><i class="bi bi-x-circle"></i> Stop sharing</button>';
                html += '</div></div>';
            });
            $("#permList").html(html);
        });
    }

    $(document).on("click", ".btnRevoke", function() {
        var id = $(this).data("id");
        confirmDialog({
            title: "Stop sharing with " + $(this).data("name") + "?",
            message: "They will immediately lose access to your financial data.",
            confirmText: "Stop sharing"
        }, function() {
            ajaxPost(PERM, { action: "revoke", advisor_id: id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") load();
            });
        });
    });
});
</script>
