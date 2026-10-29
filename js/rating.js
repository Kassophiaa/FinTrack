// Advisor ratings page
$(document).ready(function() {

    var URL = BASE_URL + "/db/rating_request.php";

    load();
    bindSearch("#searchKeyword", load);

    function load(keyword) {
        showCardLoading("#ratingList", "Loading reviews...");
        var data = keyword ? { action: "search", advisor_id: MY_ID, keyword: keyword } : { action: "display_by_advisor", advisor_id: MY_ID };
        ajaxPost(URL, data, function(response) {
            var rows = response.data || [];
            if (!keyword) renderSummary(rows);
            renderList(rows, !!keyword);
        }, function(msg) {
            $("#ratingList").html(emptyStateHtml("bi-wifi-off", "Couldn't load reviews", msg));
        });
    }

    function renderSummary(rows) {
        var counts = [0, 0, 0, 0, 0, 0], sum = 0;
        rows.forEach(function(r) { var v = parseInt(r.rating, 10) || 0; counts[v]++; sum += v; });
        var avg = rows.length ? sum / rows.length : 0;
        $("#avgRating").text(rows.length ? avg.toFixed(1) : "—");
        $("#avgStars").html(renderStars(avg));
        $("#ratingCount").text(rows.length ? "Based on " + rows.length + " rating" + (rows.length === 1 ? "" : "s") : "No ratings yet");

        var html = "";
        for (var s = 5; s >= 1; s--) {
            var pct = rows.length ? Math.round(counts[s] / rows.length * 100) : 0;
            html += '<div class="d-flex align-items-center gap-2 mb-2 small"><span style="width: 42px;">' + s + ' <i class="bi bi-star-fill" style="color:#e0a20b;"></i></span>';
            html += '<div class="bar-track flex-grow-1"><div class="bar-fill" style="width:' + pct + '%; background:#e0a20b;"></div></div>';
            html += '<span class="text-muted text-end" style="width: 28px;">' + counts[s] + '</span></div>';
        }
        $("#distribution").html(html);
    }

    function renderList(rows, isSearch) {
        if (!rows.length) {
            $("#ratingList").html(isSearch
                ? emptyStateHtml("bi-search", "No matching reviews", "Try a different keyword.")
                : emptyStateHtml("bi-star", "No reviews yet", "When clients rate your advice, their reviews will appear here."));
            return;
        }
        var html = "";
        rows.forEach(function(row) {
            html += '<div class="list-row align-items-start py-3">';
            html += '<span class="avatar sm">' + escapeHtml(initials(row.client_name)) + '</span>';
            html += '<div class="row-main"><div class="d-flex justify-content-between flex-wrap gap-2"><span class="fw-semibold">' + escapeHtml(row.client_name || 'Client') + '</span><span class="cell-sub">' + escapeHtml(formatDate(row.created_at)) + '</span></div>';
            html += '<div class="my-1">' + renderStars(row.rating) + '</div>';
            html += '<div class="text-secondary small" style="white-space: pre-wrap;">' + (row.comment ? escapeHtml(row.comment) : '<span class="text-muted fst-italic">No comment</span>') + '</div>';
            html += '</div></div>';
        });
        $("#ratingList").html(html);
    }
});
