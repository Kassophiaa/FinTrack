$(document).ready(function() {

    initDropdownCustom("investment_type_select", "investment_type_custom");
    initDropdownCustom("edit_investment_type_select", "edit_investment_type_custom");

    loadData();
    loadSummary();

    function formatCurrency(val) {
        var num = parseFloat(val) || 0;
        return '\u20B1' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function getTypeBadge(type) {
        var t = (type || '').toLowerCase().replace(/\s+/g, '-');
        var cls = 'badge-other';
        if (t === 'stocks') cls = 'badge-stocks';
        else if (t === 'mutual-funds') cls = 'badge-mutual-funds';
        else if (t === 'bonds') cls = 'badge-bonds';
        else if (t === 'crypto') cls = 'badge-crypto';
        else if (t === 'real-estate') cls = 'badge-real-estate';
        return '<span class="investment-badge ' + cls + '">' + escapeHtml(type || '-') + '</span>';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        var d = new Date(dateStr);
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var day = d.getDate();
        if (day < 10) day = '0' + day;
        return months[d.getMonth()] + ' ' + day + ', ' + d.getFullYear();
    }

    function loadSummary() {
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "investment", action: "summary" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    var d = response.data;
                    $("#totalInvested").text(formatCurrency(d.total_invested));
                    $("#currentValue").text(formatCurrency(d.total_current_value));
                    $("#totalGainLoss").text(formatCurrency(d.total_gain_loss));
                    $("#totalInvestments").text(d.total_count);

                    var returnStr = parseFloat(d.return_pct).toFixed(2) + "%";
                    $("#returnPct").text(returnStr);

                    var card = $("#gainLossCard");
                    if (d.total_gain_loss >= 0) {
                        card.removeClass("bg-danger").addClass("bg-success");
                    } else {
                        card.removeClass("bg-success").addClass("bg-danger");
                    }
                }
            },
            error: function() {}
        });
    }

    function renderTable(data) {
        var html = "";
        if (data && data.length > 0) {
            $.each(data, function(i, row) {
                var invested = parseFloat(row.amount) || 0;
                var hasCurrent = row.current_value !== null && row.current_value !== '' && row.current_value !== undefined;
                var current = hasCurrent ? parseFloat(row.current_value) : null;
                var gainLoss = current !== null ? current - invested : null;
                var returnPct = (gainLoss !== null && invested > 0) ? ((gainLoss / invested) * 100) : null;

                var glSign = (gainLoss !== null && gainLoss >= 0) ? "+" : "";
                var glClass = gainLoss !== null ? (gainLoss >= 0 ? "investment-gain" : "investment-loss") : "";
                var rpSign = (returnPct !== null && returnPct >= 0) ? "+" : "";
                var rpClass = returnPct !== null ? (returnPct >= 0 ? "investment-gain" : "investment-loss") : "";

                html += '<tr>';
                html += '<td class="col-id"><strong>' + escapeHtml(row.investment_id) + '</strong></td>';
                html += '<td class="col-type">' + getTypeBadge(row.investment_type) + '</td>';
                html += '<td class="col-name"><strong>' + escapeHtml(row.investment_name || '-') + '</strong></td>';
                html += '<td class="col-invested text-end">' + formatCurrency(row.amount) + '</td>';
                html += '<td class="col-current text-end">' + (hasCurrent ? formatCurrency(current) : '<span class="text-muted">Not updated</span>') + '</td>';
                html += '<td class="col-gainloss text-end ' + glClass + '">' + (gainLoss !== null ? glSign + formatCurrency(gainLoss) : '\u2014') + '</td>';
                html += '<td class="col-return text-center"><span class="' + rpClass + '">' + (returnPct !== null ? rpSign + parseFloat(returnPct).toFixed(2) + '%' : '\u2014') + '</span></td>';
                html += '<td class="col-account">' + escapeHtml(row.account_name || '-') + '</td>';
                html += '<td class="col-date">' + escapeHtml(formatDate(row.investment_date)) + '</td>';
                html += '<td class="col-action text-center">';
                html += '<button class="btn btn-sm btn-warning btnEdit" ';
                html += 'data-id="' + row.investment_id + '" ';
                html += 'data-account="' + row.account_id + '" ';
                html += 'data-type="' + escapeHtml(row.investment_type) + '" ';
                html += 'data-name="' + escapeHtml(row.investment_name || '') + '" ';
                html += 'data-amount="' + row.amount + '" ';
                html += 'data-current="' + (row.current_value !== null ? row.current_value : '') + '" ';
                html += 'data-desc="' + escapeHtml(row.description || '') + '" ';
                html += 'data-date="' + row.investment_date + '" ';
                html += 'title="Edit"><i class="bi bi-pencil"></i></button> ';
                html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.investment_id + '" title="Delete"><i class="bi bi-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="10" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:1.5rem;"></i><br>No investment records found</td></tr>';
        }
        $("#investmentTable").html(html);
    }

    function loadData() {
        showLoading("#investmentTable", "Loading investment records...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "investment", action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                renderTable(response.data);
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load investments. Please try again.");
                $("#investmentTable").html('<tr><td colspan="10" class="text-center text-muted">Failed to load data</td></tr>');
            }
        });
    }

    $("#investmentForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var origText = $form.find('button[type="submit"]').html();

        var investmentType = getDropdownValue("investment_type_select", "investment_type_custom");
        if (!investmentType) {
            showAlert("#alert", "danger", "Please select or enter an investment type.");
            return;
        }

        setFormLoading($form, true);

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: {
                module: "investment",
                action: "add",
                account_id: $("#account_id").val(),
                investment_type: investmentType,
                investment_name: $("#investment_name").val(),
                amount: $("#amount").val(),
                current_value: $("#current_value").val(),
                description: $("#description").val(),
                investment_date: $("#investment_date").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $form[0].reset();
                    $("#investment_type_custom").hide().val('');
                    loadData();
                    loadSummary();
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
        if (keyword === "") { loadData(); return; }

        showLoading("#investmentTable", "Searching...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "investment", action: "search", keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                renderTable(response.data);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
                $("#investmentTable").html('<tr><td colspan="10" class="text-center text-muted">Search failed</td></tr>');
            }
        });
    });

    $("#btnClear").on("click", function() {
        $("#searchKeyword").val("");
        loadData();
    });

    $("#searchKeyword").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnSearch").click();
        }
    });

    $(document).on("click", ".btnEdit", function() {
        $("#edit_investment_id").val($(this).data("id"));
        $("#edit_account_id").val($(this).data("account"));
        setDropdownValue("edit_investment_type_select", "edit_investment_type_custom", $(this).data("type"));
        $("#edit_investment_name").val($(this).data("name"));
        $("#edit_amount").val($(this).data("amount"));
        $("#edit_current_value").val($(this).data("current"));
        $("#edit_description").val($(this).data("desc"));
        $("#edit_investment_date").val($(this).data("date"));
        $("#editModal").modal("show");
    });

    $("#btnSaveEdit").on("click", function() {
        var $btn = $(this);
        var origText = $btn.html();

        var investmentType = getDropdownValue("edit_investment_type_select", "edit_investment_type_custom");
        if (!investmentType) {
            showAlert("#alert", "danger", "Please select or enter an investment type.");
            return;
        }

        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: {
                module: "investment",
                action: "edit",
                investment_id: $("#edit_investment_id").val(),
                account_id: $("#edit_account_id").val(),
                investment_type: investmentType,
                investment_name: $("#edit_investment_name").val(),
                amount: $("#edit_amount").val(),
                current_value: $("#edit_current_value").val(),
                description: $("#edit_description").val(),
                investment_date: $("#edit_investment_date").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $("#editModal").modal("hide");
                    loadData();
                    loadSummary();
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
        if (!confirm("Are you sure you want to delete this investment record?")) return;

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "investment", action: "delete", investment_id: $(this).data("id") },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    loadData();
                    loadSummary();
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

});
