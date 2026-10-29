// Client-side: advisor directory (advisors.php) and advisor profile (advisor_profile.php)
$(document).ready(function() {

    var REQ = BASE_URL + "/db/request.php";
    var PERM = BASE_URL + "/db/permission_request.php";
    var ADVICE = BASE_URL + "/db/advice_request.php";

    // -----------------------------------------------------------------------
    // Directory
    // -----------------------------------------------------------------------
    if ($("#advisorList").length) {
        loadAdvisors();
        bindSearch("#searchKeyword", loadAdvisors);
    }

    function loadAdvisors(keyword) {
        showCardLoading("#advisorList", "Loading advisors...");
        ajaxPost(REQ, { module: "advisors", action: keyword ? "search_advisors" : "display_advisors", keyword: keyword || "" }, function(response) {
            var rows = response.data || [];
            if (!rows.length) {
                $("#advisorList").html('<div class="col-12"><div class="card">' + (keyword
                    ? emptyStateHtml("bi-search", "No advisors match your search", "Try a different name or email.")
                    : emptyStateHtml("bi-people", "No advisors available yet", "Check back soon.")) + '</div></div>');
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                html += '<div class="col-sm-6 col-xl-4"><div class="card person-card">';
                html += '<span class="avatar lg">' + escapeHtml(initials(row.full_name)) + '</span>';
                html += '<div class="person-name">' + escapeHtml(row.full_name) + '</div>';
                html += '<div>' + renderStars(row.avg_rating) + ' <span class="small text-muted">' + (row.rating_count ? parseFloat(row.avg_rating).toFixed(1) + ' (' + row.rating_count + ')' : 'No ratings yet') + '</span></div>';
                html += '<div class="person-bio">' + escapeHtml(row.bio || 'This advisor has not added a bio yet.') + '</div>';
                if (row.connected) html += '<span class="pill pill-success"><i class="bi bi-check-circle-fill"></i> Connected</span>';
                html += '<a href="advisor_profile.php?advisor_id=' + row.user_id + '" class="btn ' + (row.connected ? 'btn-light' : 'btn-outline-primary') + ' btn-sm w-100">View profile</a>';
                html += '</div></div>';
            });
            $("#advisorList").html(html);
        }, function(msg) {
            $("#advisorList").html('<div class="col-12"><div class="card">' + emptyStateHtml("bi-wifi-off", "Couldn't load advisors", msg) + '</div></div>');
        });
    }

    // -----------------------------------------------------------------------
    // Profile
    // -----------------------------------------------------------------------
    if (typeof ADVISOR_ID === "undefined") return;

    var permModal = new bootstrap.Modal(document.getElementById("permissionModal"));
    var permLabels = [
        ['view_income', 'bi-arrow-down-left-circle', 'View income'],
        ['view_expenses', 'bi-arrow-up-right-circle', 'View expenses'],
        ['view_investments', 'bi-graph-up-arrow', 'View investments'],
        ['view_bank_accounts', 'bi-bank', 'View bank accounts'],
        ['can_message', 'bi-chat-dots', 'Messaging'],
        ['can_give_advice', 'bi-journal-text', 'Send advice'],
        ['can_rate', 'bi-star', 'Rating']
    ];

    loadProfile();
    loadPermission();
    loadAdvice();

    function loadProfile() {
        ajaxPost(REQ, { module: "advisors", action: "get_advisor_profile", advisor_id: ADVISOR_ID }, function(response) {
            if (response.status != "success") {
                $("#advisorName").text("Advisor not found");
                $("#accessCard, #rateCard").hide();
                toast("danger", response.message);
                return;
            }
            var d = response.data;
            $("#advisorAvatar").text(initials(d.full_name));
            $("#advisorName").text(d.full_name);
            $("#advisorEmail").text(d.email);
            $("#advisorRating").html(renderStars(d.avg_rating) + ' <span class="small text-muted">' + (d.rating_count ? parseFloat(d.avg_rating).toFixed(1) + ' from ' + d.rating_count + ' rating' + (d.rating_count == 1 ? '' : 's') : 'No ratings yet') + '</span>');
            $("#advisorBio").text(d.bio || "No bio provided.");
            $("#advisorExperience").text(d.experience || "No experience listed.");
            document.title = d.full_name + " · FinTrack";
        });
    }

    function loadPermission() {
        ajaxPost(PERM, { action: "get_permission", advisor_id: ADVISOR_ID }, function(response) {
            var p = (response.status == "success") ? response.data : null;
            if (p) {
                $("#accessNone").hide();
                $("#accessGranted").show();
                var tags = "";
                permLabels.forEach(function(l) {
                    var on = p[l[0]] == 1;
                    tags += '<span class="pill ' + (on ? 'pill-primary' : 'pill-neutral') + '"><i class="bi ' + (on ? l[1] : 'bi-lock') + '"></i> ' + l[2] + (on ? '' : ' · off') + '</span>';
                });
                $("#permTags").html(tags);
                $("#btnMessage").toggle(p.can_message == 1);
                $("#rateCard").toggle(p.can_rate == 1);
                $("#rateLocked").toggle(p.can_rate != 1);
                permLabels.forEach(function(l) { $("#perm_" + l[0]).prop("checked", p[l[0]] == 1); });
            } else {
                $("#accessGranted").hide();
                $("#accessNone").show();
                $("#rateCard, #rateLocked").hide();
            }
        });
    }

    function loadAdvice() {
        showCardLoading("#adviceList", "Loading advice...");
        ajaxPost(ADVICE, { action: "display_advisor_advice", advisor_id: ADVISOR_ID }, function(response) {
            var rows = response.data || [];
            if (!rows.length) {
                $("#adviceList").html(emptyStateHtml("bi-journal", "No advice yet", "Advice this advisor sends you will appear here."));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                html += '<div class="list-row align-items-start"><span class="icon-chip chip-primary"><i class="bi bi-lightbulb"></i></span><div class="row-main">';
                html += '<div class="fw-semibold">' + escapeHtml(row.title) + '</div>';
                html += '<div class="text-secondary small mt-1" style="white-space: pre-wrap;">' + escapeHtml(row.content) + '</div>';
                html += '<div class="cell-sub mt-1">' + escapeHtml(formatDateTime(row.created_at)) + '</div></div></div>';
            });
            $("#adviceList").html(html);
        });
    }

    $("#btnSeekAdvice, #btnEditAccess").on("click", function() { permModal.show(); });

    $("#btnSavePermission").on("click", function() {
        var $btn = $(this);
        var data = { action: "grant", advisor_id: ADVISOR_ID };
        permLabels.forEach(function(l) { data[l[0]] = $("#perm_" + l[0]).is(":checked") ? 1 : 0; });
        setButtonLoading($btn, true, "Saving...");
        ajaxPost(PERM, data, function(response) {
            setButtonLoading($btn, false);
            if (response.status == "success") {
                permModal.hide();
                toast("success", response.message);
                loadPermission();
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setButtonLoading($btn, false);
            toast("danger", msg);
        });
    });

    $("#btnRevoke").on("click", function() {
        confirmDialog({
            title: "Stop sharing with this advisor?",
            message: "They will immediately lose access to your financial data and won't be able to message you or send advice.",
            confirmText: "Stop sharing"
        }, function() {
            ajaxPost(PERM, { action: "revoke", advisor_id: ADVISOR_ID }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") loadPermission();
            });
        });
    });

    $("#rateForm").on("submit", function(e) {
        e.preventDefault();
        var rating = $("input[name=rate_rating]:checked").val();
        if (!rating) {
            toast("danger", "Please choose a star rating.");
            return;
        }
        var $form = $(this);
        setFormLoading($form, true);
        ajaxPost(ADVICE, { action: "rate_advice", advisor_id: ADVISOR_ID, rating: rating, comment: $("#rate_comment").val().trim() }, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                toast("success", "Thanks! Your rating was submitted.");
                $form[0].reset();
                loadProfile();
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setFormLoading($form, false);
            toast("danger", msg);
        });
    });
});
