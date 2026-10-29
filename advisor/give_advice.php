<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advisor.php';

$active = "advice";

require_once '../classes/AdvisorPermission.php';
$advisor = new Advisor();
$perm = new AdvisorPermission();

$title = "Give Advice";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">
        <h4 class="mb-4">Give Advice</h4>

        <div id="alert"></div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-journal-text"></i> Send Advice to Client</div>
            <div class="card-body">
                <form id="adviceForm">
                    <div class="mb-3">
                        <label class="form-label">Select Client</label>
                        <select class="form-select" id="user_id" required>
                            <option value="">-- Select Client --</option>
                            <?php
                            $permittedClients = $perm->getPermittedClientsForAdvisor($_SESSION['user_id']);
                            if ($permittedClients && $permittedClients->num_rows > 0) {
                                while ($prow = $permittedClients->fetch_assoc()) {
                                    $c = $advisor->getClientById($prow['client_id']);
                                    if ($c) {
                                        echo '<option value="' . $c['user_id'] . '">' . $c['full_name'] . ' (' . $c['username'] . ')</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" id="content" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-send"></i> Send Advice</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history"></i> Advice History</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="searchKeyword" placeholder="Search advice...">
                    <button class="btn btn-dark" id="btnSearch"><i class="bi bi-search"></i> Search</button>
                    <button class="btn btn-secondary" id="btnClear"><i class="bi bi-x-circle"></i> Clear</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="adviceTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Advice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_advice_id">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" id="edit_title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea class="form-control" id="edit_content" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dark" id="btnSaveEdit">Save</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    loadAdvice();

    function loadAdvice() {
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "display_advice" },
            dataType: "json",
            success: function(response) {
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + row.advice_id + '</td>';
                        html += '<td>' + row.client_name + '</td>';
                        html += '<td>' + row.title + '</td>';
                        html += '<td>' + row.content + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.advice_id + '" data-title="' + row.title + '" data-content="' + row.content + '"><i class="bi bi-pencil"></i></button> ';
                        html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.advice_id + '"><i class="bi bi-trash"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">No advice given yet</td></tr>';
                }
                $("#adviceTable").html(html);
            }
        });
    }

    $("#adviceForm").on("submit", function(e) {
        e.preventDefault();

        var user_id = $("#user_id").val();
        var title = $("#title").val();
        var content = $("#content").val();

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: {
                action: "add_advice",
                user_id: user_id,
                title: title,
                content: content
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#adviceForm")[0].reset();
                    loadAdvice();
                } else {
                    $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val();
        if (keyword == "") {
            loadAdvice();
            return;
        }
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "search_advice", keyword: keyword },
            dataType: "json",
            success: function(response) {
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + row.advice_id + '</td>';
                        html += '<td>' + row.client_name + '</td>';
                        html += '<td>' + row.title + '</td>';
                        html += '<td>' + row.content + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.advice_id + '" data-title="' + row.title + '" data-content="' + row.content + '"><i class="bi bi-pencil"></i></button> ';
                        html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.advice_id + '"><i class="bi bi-trash"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">No advice found</td></tr>';
                }
                $("#adviceTable").html(html);
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadAdvice();
    });

    $(document).on("click", ".btnEdit", function() {
        var id = $(this).data("id");
        var title = $(this).data("title");
        var content = $(this).data("content");
        $("#edit_advice_id").val(id);
        $("#edit_title").val(title);
        $("#edit_content").val(content);
        $("#editModal").modal("show");
    });

    $("#btnSaveEdit").on("click", function() {
        var advice_id = $("#edit_advice_id").val();
        var title = $("#edit_title").val();
        var content = $("#edit_content").val();

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: {
                action: "edit_advice",
                advice_id: advice_id,
                title: title,
                content: content
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#editModal").modal("hide");
                    loadAdvice();
                } else {
                    $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });

    $(document).on("click", ".btnDelete", function() {
        var advice_id = $(this).data("id");
        if (confirm("Are you sure you want to delete this advice?")) {
            $.ajax({
                url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
                type: "POST",
                data: { action: "delete_advice", advice_id: advice_id },
                dataType: "json",
                success: function(response) {
                    if (response.status == "success") {
                        $("#alert").html('<div class="alert alert-success">' + response.message + '</div>');
                        loadAdvice();
                    } else {
                        $("#alert").html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                }
            });
        }
    });

});
</script>
