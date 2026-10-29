$(document).ready(function() {

    if ($("#advisorList").length) {
        loadAdvisors();
    }

    if ($("#advisorName").length) {
        var params = new URLSearchParams(window.location.search);
        var advisor_id = params.get("advisor_id");

        if (advisor_id) {
            loadProfile(advisor_id);
            loadAdvisorAdvice(advisor_id);
            loadAdvisorBio(advisor_id);
            loadPermission(advisor_id);
        }
    }

    function loadAdvisors() {
        showCardLoading("#advisorList", "Loading advisors...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "advisors", action: "display_advisors" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var stars = "";
                        for (var s = 1; s <= 5; s++) {
                            stars += s <= Math.round(row.avg_rating) ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
                        }
                        html += '<div class="col-md-4 mb-4">';
                        html += '<div class="card h-100">';
                        html += '<div class="card-body text-center">';
                        html += '<i class="bi bi-person-circle" style="font-size: 3rem;"></i>';
                        html += '<h5 class="card-title mt-2">' + escapeHtml(row.full_name) + '</h5>';
                        html += '<p class="text-muted">' + escapeHtml(row.email) + '</p>';
                        html += '<p>' + stars + ' (' + escapeHtml(row.avg_rating) + '/5)</p>';
                        html += '<a href="advisor_profile.php?advisor_id=' + row.user_id + '" class="btn btn-dark btn-sm"><i class="bi bi-eye"></i> View Profile</a>';
                        html += '</div></div></div>';
                    });
                } else {
                    html = '<div class="col-12"><p class="text-center text-muted">No advisors found</p></div>';
                }
                $("#advisorList").html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load advisors. Please try again.");
                $("#advisorList").html('<div class="col-12"><p class="text-center text-danger">Failed to load advisors</p></div>');
            }
        });
    }

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val().trim();
        if (keyword === "") { loadAdvisors(); return; }
        showCardLoading("#advisorList", "Searching...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "advisors", action: "search_advisors", keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var stars = "";
                        for (var s = 1; s <= 5; s++) {
                            stars += s <= Math.round(row.avg_rating) ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
                        }
                        html += '<div class="col-md-4 mb-4">';
                        html += '<div class="card h-100">';
                        html += '<div class="card-body text-center">';
                        html += '<i class="bi bi-person-circle" style="font-size: 3rem;"></i>';
                        html += '<h5 class="card-title mt-2">' + escapeHtml(row.full_name) + '</h5>';
                        html += '<p class="text-muted">' + escapeHtml(row.email) + '</p>';
                        html += '<p>' + stars + ' (' + escapeHtml(row.avg_rating) + '/5)</p>';
                        html += '<a href="advisor_profile.php?advisor_id=' + row.user_id + '" class="btn btn-dark btn-sm"><i class="bi bi-eye"></i> View Profile</a>';
                        html += '</div></div></div>';
                    });
                } else {
                    html = '<div class="col-12"><p class="text-center text-muted">No advisors found</p></div>';
                }
                $("#advisorList").html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadAdvisors();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnSearch").click();
        }
    });

    function loadProfile(advisor_id) {
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "advisors", action: "get_advisor_profile", advisor_id: advisor_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    var d = response.data;
                    $("#advisorName").text(d.full_name);
                    $("#advisorEmail").text(d.email);
                    var stars = "";
                    for (var s = 1; s <= 5; s++) {
                        stars += s <= Math.round(d.avg_rating) ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
                    }
                    $("#advisorRating").html(stars + ' (' + escapeHtml(d.avg_rating) + '/5)');
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load advisor profile.");
            }
        });
    }

    function loadAdvisorBio(advisor_id) {
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "advisors", action: "get_advisor_profile", advisor_id: advisor_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    var d = response.data;
                    var bioHtml = '';
                    if (d.bio) bioHtml += '<p><strong>Bio:</strong> ' + escapeHtml(d.bio) + '</p>';
                    if (d.experience) bioHtml += '<p><strong>Experience:</strong> ' + escapeHtml(d.experience) + '</p>';
                    if (bioHtml) $("#advisorBio").html(bioHtml);
                }
            },
            error: function() {}
        });
    }

    function loadPermission(advisor_id) {
        $.ajax({
            url: BASE_URL + "/db/permission_request.php",
            type: "POST",
            data: { action: "get_permission", advisor_id: advisor_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success" && response.data) {
                    var p = response.data;
                    $("#perm_view_income").prop("checked", p.view_income == 1);
                    $("#perm_view_expenses").prop("checked", p.view_expenses == 1);
                    $("#perm_view_investments").prop("checked", p.view_investments == 1);
                    $("#perm_view_bank_accounts").prop("checked", p.view_bank_accounts == 1);
                    $("#perm_can_message").prop("checked", p.can_message == 1);
                    $("#perm_can_give_advice").prop("checked", p.can_give_advice == 1);
                    $("#perm_can_rate").prop("checked", p.can_rate == 1);
                    $("#btnSeekAdvice").html('<i class="bi bi-pencil"></i> Update Permissions').removeClass("btn-primary").addClass("btn-warning");
                }
            },
            error: function() {}
        });
    }

    function loadAdvisorAdvice(advisor_id) {
        $.ajax({
            url: BASE_URL + "/db/advice_request.php",
            type: "POST",
            data: { action: "display_advisor_advice", advisor_id: advisor_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.content.substring(0, 50)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted">No advice from this advisor yet</td></tr>';
                }
                $("#adviceTable").html(html);
            },
            error: function() {}
        });
    }

    $("#rateForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var origText = $form.find('button[type="submit"]').html();
        setFormLoading($form, true);

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
                    $form[0].reset();
                    var params = new URLSearchParams(window.location.search);
                    var aid = params.get("advisor_id");
                    if (aid) loadProfile(aid);
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

    // Permission Modal
    $(document).on("click", "#btnSeekAdvice", function() {
        $("#permissionModal").modal("show");
    });

    $(document).on("click", "#btnSavePermission", function() {
        var advisor_id = $("#perm_advisor_id").val();
        var $btn = $(this);
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: BASE_URL + "/db/permission_request.php",
            type: "POST",
            data: {
                action: "grant",
                advisor_id: advisor_id,
                view_income: $("#perm_view_income").is(":checked") ? 1 : 0,
                view_expenses: $("#perm_view_expenses").is(":checked") ? 1 : 0,
                view_investments: $("#perm_view_investments").is(":checked") ? 1 : 0,
                view_bank_accounts: $("#perm_view_bank_accounts").is(":checked") ? 1 : 0,
                can_message: $("#perm_can_message").is(":checked") ? 1 : 0,
                can_give_advice: $("#perm_can_give_advice").is(":checked") ? 1 : 0,
                can_rate: $("#perm_can_rate").is(":checked") ? 1 : 0
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $("#permissionModal").modal("hide");
                    loadPermission(advisor_id);
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
