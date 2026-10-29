<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Admin.php';

$active = "permissions";

$title = "Advisor Permissions";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-shield-check" style="color: var(--primary);"></i> Advisor Permissions</h4>
        </div>

        <div class="search-bar">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchKeyword" placeholder="Search permissions by client or advisor name...">
                <button class="btn btn-primary" id="btnSearch"><i class="bi bi-search"></i> Search</button>
                <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-x-circle"></i> Clear</button>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header"><i class="bi bi-shield-check"></i> All Permissions</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Client Name</th>
                                <th>Advisor Name</th>
                                <th>Income</th>
                                <th>Expenses</th>
                                <th>Investments</th>
                                <th>Bank Accts</th>
                                <th>Message</th>
                                <th>Advice</th>
                                <th>Rate</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="permTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    loadPermissions();

    function renderBadge(val) {
        return val == 1
            ? '<span class="badge bg-success"><i class="bi bi-check-lg"></i> Granted</span>'
            : '<span class="badge bg-secondary"><i class="bi bi-x-lg"></i> Denied</span>';
    }

    function loadPermissions() {
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "display_permissions" },
            dataType: "json",
            success: function(response) {
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + row.permission_id + '</td>';
                        html += '<td><strong>' + row.client_name + '</strong></td>';
                        html += '<td>' + row.advisor_name + '</td>';
                        html += '<td>' + renderBadge(row.view_income) + '</td>';
                        html += '<td>' + renderBadge(row.view_expenses) + '</td>';
                        html += '<td>' + renderBadge(row.view_investments) + '</td>';
                        html += '<td>' + renderBadge(row.view_bank_accounts) + '</td>';
                        html += '<td>' + renderBadge(row.can_message) + '</td>';
                        html += '<td>' + renderBadge(row.can_give_advice) + '</td>';
                        html += '<td>' + renderBadge(row.can_rate) + '</td>';
                        html += '<td style="color: var(--text-secondary);">' + row.created_at + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="11" class="text-center">No permissions found</td></tr>';
                }
                $("#permTable").html(html);
            }
        });
    }

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val();
        if (keyword == "") {
            loadPermissions();
            return;
        }
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "search_permissions", keyword: keyword },
            dataType: "json",
            success: function(response) {
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + row.permission_id + '</td>';
                        html += '<td><strong>' + row.client_name + '</strong></td>';
                        html += '<td>' + row.advisor_name + '</td>';
                        html += '<td>' + renderBadge(row.view_income) + '</td>';
                        html += '<td>' + renderBadge(row.view_expenses) + '</td>';
                        html += '<td>' + renderBadge(row.view_investments) + '</td>';
                        html += '<td>' + renderBadge(row.view_bank_accounts) + '</td>';
                        html += '<td>' + renderBadge(row.can_message) + '</td>';
                        html += '<td>' + renderBadge(row.can_give_advice) + '</td>';
                        html += '<td>' + renderBadge(row.can_rate) + '</td>';
                        html += '<td style="color: var(--text-secondary);">' + row.created_at + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="11" class="text-center">No permissions found</td></tr>';
                }
                $("#permTable").html(html);
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadPermissions();
    });

});
</script>
