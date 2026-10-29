<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Admin.php';

$active = "users";

$title = "Manage Users";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-people" style="color: var(--primary);"></i> Manage Users</h4>
        </div>

        <div id="alert"></div>

        <div class="search-bar">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchKeyword" placeholder="Search by name, username, or email...">
                <button class="btn btn-primary" id="btnSearch"><i class="bi bi-search"></i> Search</button>
                <button class="btn btn-outline-secondary" id="btnClear"><i class="bi bi-x-circle"></i> Clear</button>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header"><i class="bi bi-people"></i> All Users</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Update Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_user_id">
                <label class="form-label">Select Role</label>
                <select class="form-select" id="edit_role">
                    <option value="user">User</option>
                    <option value="advisor">Advisor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveRole"><i class="bi bi-check-lg"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    loadUsers();

    function renderRows(data) {
        var html = "";
        if (data.length > 0) {
            $.each(data, function(i, row) {
                var roleClass = row.role == 'admin' ? 'bg-danger' : (row.role == 'advisor' ? 'bg-primary' : 'bg-secondary');
                html += '<tr>';
                html += '<td>' + row.user_id + '</td>';
                html += '<td><strong>' + escapeHtml(row.full_name) + '</strong></td>';
                html += '<td>' + escapeHtml(row.username) + '</td>';
                html += '<td style="color: var(--text-secondary);">' + escapeHtml(row.email) + '</td>';
                html += '<td>' + escapeHtml(row.phone || '') + '</td>';
                var statusClass = row.status == 'Active' ? 'bg-success' : 'bg-secondary';
                var toggleClass = row.status == 'Active' ? 'btn-outline-danger' : 'btn-outline-success';
                var toggleIcon = row.status == 'Active' ? 'bi bi-toggle-on' : 'bi bi-toggle-off';
                var toggleTitle = row.status == 'Active' ? 'Deactivate' : 'Activate';
                html += '<td><span class="badge ' + roleClass + '">' + row.role + '</span></td>';
                html += '<td><span class="badge ' + statusClass + '">' + row.status + '</span></td>';
                html += '<td>';
                html += '<button class="btn btn-sm btn-outline-primary btnEditRole" data-id="' + row.user_id + '" data-role="' + row.role + '"><i class="bi bi-pencil"></i></button> ';
                html += '<button class="btn btn-sm ' + toggleClass + ' btnToggleStatus" data-id="' + row.user_id + '" data-status="' + row.status + '" title="' + toggleTitle + '"><i class="' + toggleIcon + '"></i></button>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="8" class="text-center">No users found</td></tr>';
        }
        return html;
    }

    function loadUsers() {
        showCardLoading("#alert", "Loading users...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "display" },
            dataType: "json",
            success: function(response) {
                $("#alert").html("");
                $("#userTable").html(renderRows(response.data));
            }
        });
    }

    function searchUsers() {
        var keyword = $("#searchKeyword").val();
        if (keyword == "") {
            loadUsers();
            return;
        }
        showCardLoading("#alert", "Searching...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "search", keyword: keyword },
            dataType: "json",
            success: function(response) {
                $("#alert").html("");
                $("#userTable").html(renderRows(response.data));
            }
        });
    }

    $("#btnSearch").on("click", function() {
        searchUsers();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which == 13) {
            e.preventDefault();
            searchUsers();
        }
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadUsers();
    });

    $(document).on("click", ".btnEditRole", function() {
        var user_id = $(this).data("id");
        var role = $(this).data("role");
        $("#edit_user_id").val(user_id);
        $("#edit_role").val(role);
        $("#roleModal").modal("show");
    });

    $("#btnSaveRole").on("click", function() {
        var user_id = $("#edit_user_id").val();
        var role = $("#edit_role").val();

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/admin_request.php",
            type: "POST",
            data: { action: "update_role", user_id: user_id, role: role },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#roleModal").modal("hide");
                    loadUsers();
                } else {
                    $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });

    $(document).on("click", ".btnToggleStatus", function(e) {
        e.preventDefault();
        var btn = $(this);
        var user_id = btn.data("id");
        var currentStatus = btn.data("status");
        var newAction = currentStatus == 'Active' ? 'deactivate' : 'activate';
        if (confirm("Are you sure you want to " + newAction + " this user?")) {
            btn.prop("disabled", true);
            $.ajax({
                url: "<?php echo BASE_URL; ?>/db/admin_request.php",
                type: "POST",
                data: { action: "toggle_status", user_id: user_id },
                dataType: "json",
                success: function(response) {
                    if (response.status == "success") {
                        $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                        loadUsers();
                    } else {
                        $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $("#alert").html('<div class="alert alert-danger">Request failed. Please try again.</div>');
                },
                complete: function() {
                    btn.prop("disabled", false);
                }
            });
        }
    });

});
</script>
