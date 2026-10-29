<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "users";

$title = "Users";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Users</h1>
        <p>Manage roles and account access for everyone on the platform.</p>
    </div>
</div>

<div class="card table-card">
    <div class="card-header">
        <div class="toolbar w-100">
            <div class="search-input" style="max-width: 320px;">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchKeyword" placeholder="Search name, username or email…">
                <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
            </div>
            <select class="form-select" id="roleFilter" style="width: auto;">
                <option value="">All roles</option>
                <option value="user">Clients</option>
                <option value="advisor">Advisors</option>
                <option value="admin">Administrators</option>
            </select>
            <select class="form-select" id="statusFilter" style="width: auto;">
                <option value="">Any status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <span class="ms-auto small text-muted" id="userCount"></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="userTable"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_user_id">
                <p class="small text-secondary mb-2">User: <strong id="roleUserName"></strong></p>
                <label class="form-label" for="edit_role">Role</label>
                <select class="form-select" id="edit_role">
                    <option value="user">Client</option>
                    <option value="advisor">Advisor</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveRole">Save</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var URL = BASE_URL + "/db/admin_request.php";
    var rows = [];
    var roleModal = new bootstrap.Modal(document.getElementById("roleModal"));
    var roleNames = { user: 'Client', advisor: 'Advisor', admin: 'Administrator' };
    var rolePills = { user: 'pill-neutral', advisor: 'pill-info', admin: 'pill-danger' };

    load();
    bindSearch("#searchKeyword", load);
    $("#roleFilter, #statusFilter").on("change", render);

    function load(keyword) {
        showLoading("#userTable", "Loading users...");
        ajaxPost(URL, { action: keyword ? "search" : "display", keyword: keyword || "" }, function(response) {
            rows = response.data || [];
            render();
        }, function(msg) {
            $("#userTable").html(emptyRow(5, "bi-wifi-off", "Couldn't load users", msg));
        });
    }

    function render() {
        var role = $("#roleFilter").val(), status = $("#statusFilter").val();
        var list = rows.filter(function(r) { return (!role || r.role === role) && (!status || r.status === status); });
        $("#userCount").text(list.length + " of " + rows.length + " users");
        if (!list.length) {
            $("#userTable").html(emptyRow(5, "bi-people", "No users found", "Try changing the search or filters."));
            return;
        }
        var html = "";
        list.forEach(function(row) {
            var isMe = row.user_id == MY_ID;
            var active = row.status === 'Active';
            html += '<tr>';
            html += '<td><div class="d-flex align-items-center gap-2"><span class="avatar sm">' + escapeHtml(initials(row.full_name)) + '</span><div><div class="cell-title">' + escapeHtml(row.full_name) + (isMe ? ' <span class="pill pill-primary ms-1">You</span>' : '') + '</div><div class="cell-sub">@' + escapeHtml(row.username) + ' · ' + escapeHtml(row.email) + '</div></div></div></td>';
            html += '<td>' + escapeHtml(row.phone || '—') + '</td>';
            html += '<td><span class="pill ' + (rolePills[row.role] || 'pill-neutral') + '">' + escapeHtml(roleNames[row.role] || row.role) + '</span></td>';
            html += '<td><span class="pill ' + (active ? 'pill-success' : 'pill-neutral') + '"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> ' + escapeHtml(row.status) + '</span></td>';
            html += '<td class="text-end">';
            if (!isMe) {
                html += '<div class="d-inline-flex gap-2">';
                html += '<button class="btn btn-light btn-sm btnEditRole" data-id="' + row.user_id + '">Change role</button>';
                html += '<button class="btn btn-sm ' + (active ? 'btn-outline-danger' : 'btn-outline-primary') + ' btnToggleStatus" data-id="' + row.user_id + '">' + (active ? 'Deactivate' : 'Activate') + '</button>';
                html += '</div>';
            } else {
                html += '<span class="small text-muted">—</span>';
            }
            html += '</td></tr>';
        });
        $("#userTable").html(html);
    }

    function findRow(id) {
        return rows.filter(function(r) { return r.user_id == id; })[0];
    }

    $(document).on("click", ".btnEditRole", function() {
        var row = findRow($(this).data("id"));
        $("#edit_user_id").val(row.user_id);
        $("#roleUserName").text(row.full_name);
        $("#edit_role").val(row.role);
        roleModal.show();
    });

    $("#btnSaveRole").on("click", function() {
        var $btn = $(this);
        setButtonLoading($btn, true);
        ajaxPost(URL, { action: "update_role", user_id: $("#edit_user_id").val(), role: $("#edit_role").val() }, function(response) {
            setButtonLoading($btn, false);
            if (response.status == "success") {
                roleModal.hide();
                toast("success", response.message);
                load($("#searchKeyword").val().trim());
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setButtonLoading($btn, false);
            toast("danger", msg);
        });
    });

    $(document).on("click", ".btnToggleStatus", function() {
        var row = findRow($(this).data("id"));
        var deactivate = row.status === 'Active';
        confirmDialog({
            title: (deactivate ? "Deactivate " : "Activate ") + row.full_name + "?",
            message: deactivate ? "They will be signed out and won't be able to log in until reactivated." : "They will be able to sign in again.",
            confirmText: deactivate ? "Deactivate" : "Activate",
            confirmClass: deactivate ? "btn-danger" : "btn-primary"
        }, function() {
            ajaxPost(URL, { action: "toggle_status", user_id: row.user_id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") load($("#searchKeyword").val().trim());
            });
        });
    });
});
</script>
