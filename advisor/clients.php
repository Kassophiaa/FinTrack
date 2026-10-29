<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "clients";

$title = "Clients";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-people" style="color: var(--primary);"></i> Clients</h4>
        </div>

        <div id="alert"></div>

        <div class="search-bar">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchKeyword" placeholder="Search clients by name, username, or email...">
                <button class="btn btn-primary" id="btnSearch">Search</button>
                <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-x-circle"></i> Clear</button>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header"><i class="bi bi-people"></i> Permitted Clients</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="clientTable">
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

    loadClients();

    function loadClients() {
        showLoading("#clientTable", "Loading clients...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "display_clients" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.user_id) + '</td>';
                        html += '<td><strong>' + escapeHtml(row.full_name) + '</strong></td>';
                        html += '<td>' + escapeHtml(row.username) + '</td>';
                        html += '<td style="color: var(--text-secondary);">' + escapeHtml(row.email) + '</td>';
                        html += '<td><a href="client_profile.php?user_id=' + row.user_id + '" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted">No clients found</td></tr>';
                }
                $("#clientTable").html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load clients. Please try again.");
                $("#clientTable").html('<tr><td colspan="5" class="text-center text-danger">Failed to load clients</td></tr>');
            }
        });
    }

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val().trim();
        if (keyword === "") {
            loadClients();
            return;
        }
        showLoading("#clientTable", "Searching...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "search_clients", keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.user_id) + '</td>';
                        html += '<td><strong>' + escapeHtml(row.full_name) + '</strong></td>';
                        html += '<td>' + escapeHtml(row.username) + '</td>';
                        html += '<td style="color: var(--text-secondary);">' + escapeHtml(row.email) + '</td>';
                        html += '<td><a href="client_profile.php?user_id=' + row.user_id + '" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a></td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted">No clients found</td></tr>';
                }
                $("#clientTable").html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadClients();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnSearch").click();
        }
    });

});
</script>
