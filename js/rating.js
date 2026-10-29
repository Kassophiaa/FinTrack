$(document).ready(function() {

    if ($("#ratingTable").length) {
        loadRatings();
    }

    function loadRatings() {
        showLoading("#ratingTable", "Loading ratings...");
        $.ajax({
            url: BASE_URL + "/db/rating_request.php",
            type: "POST",
            data: { action: "display_by_advisor", advisor_id: MY_ID },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var stars = "";
                        for (var s = 1; s <= 5; s++) {
                            stars += s <= row.rating ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
                        }
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.rating_id) + '</td>';
                        html += '<td>' + escapeHtml(row.client_name) + '</td>';
                        html += '<td>' + stars + ' (' + escapeHtml(String(row.rating)) + '/5)</td>';
                        html += '<td>' + escapeHtml(row.comment.substring(0, 40)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-client="' + escapeHtml(row.client_name) + '" data-rating="' + row.rating + '" data-comment="' + escapeHtml(row.comment) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No ratings received yet</td></tr>';
                }
                $("#ratingTable").html(html);
            },
            error: function() {
                $("#ratingTable").html('<tr><td colspan="6" class="text-center text-danger">Failed to load ratings</td></tr>');
            }
        });
    }

    $("#btnSearch").on("click", function() {
        var keyword = $("#searchKeyword").val();
        if (keyword.trim() == "") { loadRatings(); return; }

        showLoading("#ratingTable", "Searching...");
        $.ajax({
            url: BASE_URL + "/db/rating_request.php",
            type: "POST",
            data: { action: "search", advisor_id: MY_ID, keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var stars = "";
                        for (var s = 1; s <= 5; s++) {
                            stars += s <= row.rating ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
                        }
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.rating_id) + '</td>';
                        html += '<td>' + escapeHtml(row.client_name) + '</td>';
                        html += '<td>' + stars + ' (' + escapeHtml(String(row.rating)) + '/5)</td>';
                        html += '<td>' + escapeHtml(row.comment.substring(0, 40)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info btnView" data-client="' + escapeHtml(row.client_name) + '" data-rating="' + row.rating + '" data-comment="' + escapeHtml(row.comment) + '" data-date="' + escapeHtml(row.created_at) + '"><i class="bi bi-eye"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No ratings found</td></tr>';
                }
                $("#ratingTable").html(html);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadRatings();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnSearch").click();
        }
    });

    $(document).on("click", ".btnView", function() {
        var rating = $(this).data("rating");
        var stars = "";
        for (var s = 1; s <= 5; s++) {
            stars += s <= rating ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
        }
        $("#viewClient").text($(this).data("client"));
        $("#viewRating").html(stars + ' (' + rating + '/5)');
        $("#viewComment").text($(this).data("comment"));
        $("#viewDate").text($(this).data("date"));
        $("#viewModal").modal("show");
    });

});
