$(document).ready(function() {

    if ($("#advisorAdviceTable").length || $("#clientAdviceTable").length || $(".btnRate").length) {
        loadAdvice();
    }

    if ($("#advisorAdviceTable").length) {
        loadAdvisorAdviceHistory();
    }

    function loadAdvice() {
        var tableId = "adviceTable";
        if ($("#advisorAdviceTable").length) tableId = "advisorAdviceTable";
        if ($("#clientAdviceTable").length) tableId = "clientAdviceTable";

        var isAdvisor = $("#advisorAdviceTable").length > 0;
        var url = isAdvisor ? BASE_URL + "/db/advisor_request.php" : BASE_URL + "/db/advice_request.php";
        var action = "display_advice";

        showLoading("#" + tableId, "Loading advice...");
        $.ajax({
            url: url,
            type: "POST",
            data: { action: action },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.advice_id) + '</td>';
                        html += '<td>' + escapeHtml(row.advisor_name || row.client_name || '') + '</td>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.content.substring(0, 40)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-name="' + escapeHtml(row.advisor_name || row.client_name || '') + '" data-title="' + escapeHtml(row.title) + '" data-content="' + escapeHtml(row.content) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button> ';
                        if (isAdvisor) {
                            html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.advice_id + '" data-title="' + escapeHtml(row.title) + '" data-content="' + escapeHtml(row.content) + '"><i class="bi bi-pencil"></i></button> ';
                            html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.advice_id + '"><i class="bi bi-trash"></i></button>';
                        } else {
                            html += '<button class="btn btn-sm btn-warning btnRate" data-advisor_id="' + row.advisor_id + '"><i class="bi bi-star"></i> Rate</button>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No advice found</td></tr>';
                }
                $("#" + tableId).html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load advice.");
                $("#" + tableId).html('<tr><td colspan="6" class="text-center text-danger">Failed to load data</td></tr>');
            }
        });
    }

    function loadAdvisorAdviceHistory() {
        $.ajax({
            url: BASE_URL + "/db/advisor_request.php",
            type: "POST",
            data: { action: "display_advice" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.advice_id) + '</td>';
                        html += '<td>' + escapeHtml(row.client_name) + '</td>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.content.substring(0, 40)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-name="' + escapeHtml(row.client_name) + '" data-title="' + escapeHtml(row.title) + '" data-content="' + escapeHtml(row.content) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button> ';
                        html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.advice_id + '" data-title="' + escapeHtml(row.title) + '" data-content="' + escapeHtml(row.content) + '"><i class="bi bi-pencil"></i></button> ';
                        html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.advice_id + '"><i class="bi bi-trash"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No advice given yet</td></tr>';
                }
                $("#advisorAdviceTable").html(html);
            },
            error: function() {}
        });
    }

    $("#adviceForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var origText = $form.find('button[type="submit"]').html();
        setFormLoading($form, true);

        $.ajax({
            url: BASE_URL + "/db/advisor_request.php",
            type: "POST",
            data: {
                action: "add_advice",
                user_id: $("#user_id").val(),
                title: $("#title").val(),
                content: $("#content").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $form[0].reset();
                    loadAdvisorAdviceHistory();
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred. Please try again.");
            },
            complete: function() {
                setFormLoading($form, false, origText);
            }
        });
    });

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val().trim();
        if (keyword === "") { loadAdvice(); return; }
        var isAdvisor = $("#advisorAdviceTable").length > 0;
        var url = isAdvisor ? BASE_URL + "/db/advisor_request.php" : BASE_URL + "/db/advice_request.php";
        var action = "search_advice";

        showLoading("#" + (isAdvisor ? "advisorAdviceTable" : ($("#clientAdviceTable").length ? "clientAdviceTable" : "adviceTable")), "Searching...");
        $.ajax({
            url: url,
            type: "POST",
            data: { action: action, keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.advice_id) + '</td>';
                        html += '<td>' + escapeHtml(row.advisor_name || row.client_name || '') + '</td>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.content.substring(0, 40)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-name="' + escapeHtml(row.advisor_name || row.client_name || '') + '" data-title="' + escapeHtml(row.title) + '" data-content="' + escapeHtml(row.content) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button> ';
                        if (isAdvisor) {
                            html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.advice_id + '" data-title="' + escapeHtml(row.title) + '" data-content="' + escapeHtml(row.content) + '"><i class="bi bi-pencil"></i></button> ';
                            html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.advice_id + '"><i class="bi bi-trash"></i></button>';
                        } else {
                            html += '<button class="btn btn-sm btn-warning btnRate" data-advisor_id="' + row.advisor_id + '"><i class="bi bi-star"></i> Rate</button>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No advice found</td></tr>';
                }
                var tableId = isAdvisor ? "advisorAdviceTable" : "adviceTable";
                if ($("#clientAdviceTable").length) tableId = "clientAdviceTable";
                $("#" + tableId).html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadAdvice();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnSearch").click();
        }
    });

    $(document).on("click", ".btnView", function() {
        var nameLabel = $("#viewAdvisor").length ? "viewAdvisor" : "viewClient";
        if ($("#viewClient").length) nameLabel = "viewClient";
        if ($("#viewAdvisor").length) nameLabel = "viewAdvisor";
        $("#" + nameLabel).text($(this).data("name"));
        $("#viewTitle").text($(this).data("title"));
        $("#viewContent").text($(this).data("content"));
        $("#viewDate").text($(this).data("date"));
        $("#viewModal").modal("show");
    });

    $(document).on("click", ".btnEdit", function() {
        $("#edit_advice_id").val($(this).data("id"));
        $("#edit_title").val($(this).data("title"));
        $("#edit_content").val($(this).data("content"));
        $("#editModal").modal("show");
    });

    $("#btnSaveEdit").on("click", function() {
        var $btn = $(this);
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: BASE_URL + "/db/advisor_request.php",
            type: "POST",
            data: {
                action: "edit_advice",
                advice_id: $("#edit_advice_id").val(),
                title: $("#edit_title").val(),
                content: $("#edit_content").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $("#editModal").modal("hide");
                    loadAdvisorAdviceHistory();
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred. Please try again.");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origText);
            }
        });
    });

    $(document).on("click", ".btnDelete", function() {
        if (!confirm("Are you sure you want to delete this advice?")) return;

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/advisor_request.php",
            type: "POST",
            data: { action: "delete_advice", advice_id: $(this).data("id") },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    loadAdvisorAdviceHistory();
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred. Please try again.");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origHtml);
            }
        });
    });

    $(document).on("click", ".btnRate", function() {
        $("#rate_advisor_id").val($(this).data("advisor_id"));
        $("#rate_rating").val("5");
        $("#rate_comment").val("");
        $("#rateModal").modal("show");
    });

    $("#btnSubmitRating").on("click", function() {
        var $btn = $(this);
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        $.ajax({
            url: BASE_URL + "/db/advice_request.php",
            type: "POST",
            data: {
                action: "rate_advice",
                advisor_id: $("#rate_advisor_id").val(),
                rating: $("#rate_rating").val(),
                comment: $("#rate_comment").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $("#rateModal").modal("hide");
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred. Please try again.");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origText);
            }
        });
    });

});
