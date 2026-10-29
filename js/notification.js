$(document).ready(function() {

    if ($("#notifTable").length) {
        loadNotifications();
    }

    function loadNotifications() {
        showLoading("#notifTable", "Loading notifications...");
        $.ajax({
            url: BASE_URL + "/db/notification_request.php",
            type: "POST",
            data: { action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var statusBadge = row.is_read == 0 ? '<span class="badge bg-danger">Unread</span>' : '<span class="badge bg-success">Read</span>';
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.notification_id) + '</td>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.message.substring(0, 50)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>' + statusBadge + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-id="' + row.notification_id + '" data-title="' + escapeHtml(row.title) + '" data-message="' + escapeHtml(row.message) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button> ';
                        if (row.is_read == 0) {
                            html += '<button class="btn btn-sm btn-warning btnRead" data-id="' + row.notification_id + '"><i class="bi bi-check"></i></button> ';
                        }
                        html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.notification_id + '"><i class="bi bi-trash"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No notifications</td></tr>';
                }
                $("#notifTable").html(html);
            },
            error: function() {
                $("#notifTable").html('<tr><td colspan="6" class="text-center text-danger">Failed to load notifications</td></tr>');
            }
        });
    }

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val();
        if (keyword.trim() == "") { loadNotifications(); return; }

        showLoading("#notifTable", "Searching...");
        $.ajax({
            url: BASE_URL + "/db/notification_request.php",
            type: "POST",
            data: { action: "search", keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var statusBadge = row.is_read == 0 ? '<span class="badge bg-danger">Unread</span>' : '<span class="badge bg-success">Read</span>';
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.notification_id) + '</td>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.message.substring(0, 50)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>' + statusBadge + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-id="' + row.notification_id + '" data-title="' + escapeHtml(row.title) + '" data-message="' + escapeHtml(row.message) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button> ';
                        if (row.is_read == 0) {
                            html += '<button class="btn btn-sm btn-warning btnRead" data-id="' + row.notification_id + '"><i class="bi bi-check"></i></button> ';
                        }
                        html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.notification_id + '"><i class="bi bi-trash"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No notifications found</td></tr>';
                }
                $("#notifTable").html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadNotifications();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnSearch").click();
        }
    });

    $("#btnMarkAll").on("click", function() {
        var $btn = $(this);
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/notification_request.php",
            type: "POST",
            data: { action: "mark_all_read" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    loadNotifications();
                } else {
                    showAlert("#alert", "danger", response.message || "Failed");
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origText);
            }
        });
    });

    $(document).on("click", ".btnView", function() {
        $("#viewTitle").text($(this).data("title"));
        $("#viewMessage").text($(this).data("message"));
        $("#viewDate").text($(this).data("date"));
        $("#viewModal").modal("show");
    });

    $(document).on("click", ".btnRead", function() {
        var id = $(this).data("id");
        var $btn = $(this);
        $btn.prop("disabled", true);

        $.ajax({
            url: BASE_URL + "/db/notification_request.php",
            type: "POST",
            data: { action: "mark_read", notification_id: id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    loadNotifications();
                } else {
                    showAlert("#alert", "danger", response.message || "Failed");
                    $btn.prop("disabled", false);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred");
                $btn.prop("disabled", false);
            }
        });
    });

    $(document).on("click", ".btnDelete", function() {
        if (!confirm("Are you sure you want to delete this notification?")) return;

        var id = $(this).data("id");
        var $btn = $(this);
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/notification_request.php",
            type: "POST",
            data: { action: "delete", notification_id: id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    loadNotifications();
                } else {
                    showAlert("#alert", "danger", response.message || "Failed");
                    $btn.prop("disabled", false).html('<i class="bi bi-trash"></i>');
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred");
                $btn.prop("disabled", false).html('<i class="bi bi-trash"></i>');
            }
        });
    });

});
