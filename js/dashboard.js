$(document).ready(function() {

    if ($("#totalIncome").length) {
        loadDashboardStats();
    }

    if ($("#recentAdviceTable").length) {
        loadRecentAdvice();
    }

    function loadDashboardStats() {
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "income", action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var total = 0;
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        total += parseFloat(row.amount);
                    });
                }
                $("#totalIncome").text(formatCurrency(total));
            },
            error: function() {
                $("#totalIncome").text("--");
            }
        });

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "expense", action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var total = 0;
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        total += parseFloat(row.amount);
                    });
                }
                $("#totalExpense").text(formatCurrency(total));
            },
            error: function() {
                $("#totalExpense").text("--");
            }
        });

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "investment", action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var total = 0;
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        total += parseFloat(row.amount);
                    });
                }
                $("#totalInvestment").text(formatCurrency(total));
            },
            error: function() {
                $("#totalInvestment").text("--");
            }
        });
    }

    function loadRecentAdvice() {
        $.ajax({
            url: BASE_URL + "/db/advice_request.php",
            type: "POST",
            data: { action: "display_advice" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    var count = 0;
                    $.each(response.data, function(i, row) {
                        if (count >= 5) return false;
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.title) + '</td>';
                        html += '<td>' + escapeHtml(row.content.substring(0, 60)) + '...</td>';
                        html += '<td>' + escapeHtml(row.created_at) + '</td>';
                        html += '</tr>';
                        count++;
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted">No advice yet</td></tr>';
                }
                $("#recentAdviceTable").html(html);
            },
            error: function() {
                $("#recentAdviceTable").html('<tr><td colspan="3" class="text-center text-danger">Failed to load data</td></tr>');
            }
        });
    }

});
