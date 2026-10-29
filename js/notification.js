$(document).ready(function() {

    var URL = BASE_URL + "/db/notification_request.php";
    var rows = [];
    var filter = "all";
    var icons = {
        'New advice received': ['bi-journal-text', 'chip-primary'],
        'New rating': ['bi-star', 'chip-warning'],
        'New client': ['bi-person-plus', 'chip-success'],
        'Access removed': ['bi-person-dash', 'chip-danger'],
        'Permission Updated': ['bi-shield-check', 'chip-info']
    };

    load();
    bindSearch("#searchKeyword", load);

    $("#notifFilter .nav-link").on("click", function() {
        $("#notifFilter .nav-link").removeClass("active");
        $(this).addClass("active");
        filter = $(this).data("filter");
        render();
    });

    function load(keyword) {
        showCardLoading("#notifList", "Loading notifications...");
        ajaxPost(URL, { action: keyword ? "search" : "display", keyword: keyword || "" }, function(response) {
            rows = response.data || [];
            render(!!keyword);
        }, function(msg) {
            $("#notifList").html(emptyStateHtml("bi-wifi-off", "Couldn't load notifications", msg));
        });
    }

    function render(isSearch) {
        var unread = rows.filter(function(r) { return r.is_read == 0; }).length;
        $("#unreadCount").text(unread).toggle(unread > 0);
        $("#btnMarkAll").prop("disabled", unread === 0);

        var list = filter === "unread" ? rows.filter(function(r) { return r.is_read == 0; }) : rows;
        if (!list.length) {
            $("#notifList").html(isSearch
                ? emptyStateHtml("bi-search", "No matching notifications", "Try a different keyword.")
                : (filter === "unread"
                    ? emptyStateHtml("bi-check2-circle", "You're all caught up", "There are no unread notifications.")
                    : emptyStateHtml("bi-bell", "No notifications yet", "We'll let you know when something happens.")));
            return;
        }
        var html = "";
        list.forEach(function(row) {
            var icon = icons[row.title] || ['bi-bell', 'chip-neutral'];
            var isUnread = row.is_read == 0;
            html += '<div class="notif-item ' + (isUnread ? 'unread' : '') + '">';
            html += '<span class="icon-chip ' + icon[1] + '"><i class="bi ' + icon[0] + '"></i></span>';
            html += '<div class="notif-main"><div class="notif-title">' + escapeHtml(row.title) + (isUnread ? ' <span class="new-dot" title="Unread"></span>' : '') + '</div>';
            html += '<div class="notif-text">' + escapeHtml(row.message) + '</div>';
            html += '<div class="notif-time" title="' + escapeHtml(formatDateTime(row.created_at)) + '">' + escapeHtml(timeAgo(row.created_at)) + '</div></div>';
            html += '<div class="row-actions">';
            if (isUnread) html += '<button class="btn-icon success btnRead" data-id="' + row.notification_id + '" title="Mark as read" aria-label="Mark as read"><i class="bi bi-check2"></i></button>';
            html += '<button class="btn-icon danger btnDelete" data-id="' + row.notification_id + '" title="Delete" aria-label="Delete"><i class="bi bi-trash"></i></button>';
            html += '</div></div>';
        });
        $("#notifList").html(html);
    }

    function setRead(id) {
        rows.forEach(function(r) { if (r.notification_id == id) r.is_read = 1; });
    }

    function refreshBadges() {
        var unread = rows.filter(function(r) { return r.is_read == 0; }).length;
        $(".sidebar-link.active .nav-count").text(unread).toggle(unread > 0);
        $('.topbar-icon[title="Notifications"] .dot-count').text(unread).toggle(unread > 0);
    }

    $("#btnMarkAll").on("click", function() {
        var $btn = $(this);
        setButtonLoading($btn, true);
        ajaxPost(URL, { action: "mark_all_read" }, function(response) {
            setButtonLoading($btn, false);
            if (response.status == "success") {
                rows.forEach(function(r) { r.is_read = 1; });
                render();
                refreshBadges();
                toast("success", response.message);
            } else {
                toast("danger", response.message);
            }
        });
    });

    $(document).on("click", ".btnRead", function() {
        var id = $(this).data("id");
        ajaxPost(URL, { action: "mark_read", notification_id: id }, function(response) {
            if (response.status == "success") {
                setRead(id);
                render();
                refreshBadges();
            } else {
                toast("danger", response.message);
            }
        });
    });

    $(document).on("click", ".btnDelete", function() {
        var id = $(this).data("id");
        confirmDialog({ title: "Delete this notification?", message: "It will be removed from your list." }, function() {
            ajaxPost(URL, { action: "delete", notification_id: id }, function(response) {
                if (response.status == "success") {
                    rows = rows.filter(function(r) { return r.notification_id != id; });
                    render();
                    refreshBadges();
                    toast("success", "Notification deleted");
                } else {
                    toast("danger", response.message);
                }
            });
        });
    });
});
