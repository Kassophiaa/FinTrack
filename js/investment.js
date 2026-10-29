$(document).ready(function() {

    var URL = BASE_URL + "/db/request.php";
    var rowsById = {};
    var modal = new bootstrap.Modal(document.getElementById("invModal"));
    var typePills = { 'Stocks': 'pill-info', 'Mutual Funds': 'pill-primary', 'Bonds': 'pill-success', 'Crypto': 'pill-warning', 'Real Estate': 'pill-neutral' };

    initDropdownCustom("inv_type_select", "inv_type_custom");
    loadData();
    loadSummary();
    bindSearch("#searchKeyword", function(keyword) { loadData(keyword); });

    function loadSummary() {
        ajaxPost(URL, { module: "investment", action: "summary" }, function(response) {
            if (response.status != "success") return;
            var d = response.data;
            var gl = parseFloat(d.total_gain_loss) || 0;
            var pct = parseFloat(d.return_pct) || 0;
            $("#totalInvested").text(formatCurrency(d.total_invested));
            $("#currentValue").text(formatCurrency(d.total_current_value));
            $("#totalGainLoss").text((gl >= 0 ? "+" : "") + formatCurrency(gl)).toggleClass("text-success", gl > 0).toggleClass("text-danger", gl < 0);
            $("#returnPct").text(d.total_count > 0 ? (pct >= 0 ? "+" : "") + pct.toFixed(2) + "% overall return" : "");
            $("#gainChip").attr("class", "icon-chip " + (gl > 0 ? "chip-success" : (gl < 0 ? "chip-danger" : "chip-neutral")))
                .html('<i class="bi ' + (gl < 0 ? 'bi-graph-down' : 'bi-graph-up') + '"></i>');
            $("#totalInvestments").text(d.total_count);
        });
    }

    function loadData(keyword) {
        showLoading("#investmentTable", "Loading...");
        var data = keyword ? { module: "investment", action: "search", keyword: keyword } : { module: "investment", action: "display" };
        ajaxPost(URL, data, function(response) {
            renderTable(response.data || [], !!keyword);
        }, function(msg) {
            $("#investmentTable").html(emptyRow(8, "bi-wifi-off", "Couldn't load investments", msg));
        });
    }

    function renderTable(rows, isSearch) {
        rowsById = {};
        if (!rows.length) {
            var action = (!isSearch && HAS_ACCOUNTS) ? '<button class="btn btn-primary btn-sm" onclick="$(\'#btnAdd\').click()"><i class="bi bi-plus-lg"></i> Add investment</button>' : '';
            $("#investmentTable").html(isSearch
                ? emptyRow(8, "bi-search", "No matching investments", "Try a different keyword.")
                : emptyRow(8, "bi-graph-up-arrow", "No investments yet", "Record stocks, funds, crypto and more to see how they perform.", action));
            return;
        }
        var html = "";
        rows.forEach(function(row) {
            rowsById[row.investment_id] = row;
            var invested = parseFloat(row.amount) || 0;
            var hasCurrent = row.current_value !== null && row.current_value !== '' && row.current_value !== undefined;
            var current = hasCurrent ? parseFloat(row.current_value) : null;
            var gl = hasCurrent ? current - invested : null;
            var pct = (gl !== null && invested > 0) ? (gl / invested) * 100 : null;
            var glCls = gl === null ? 'text-muted' : (gl >= 0 ? 'text-success' : 'text-danger');

            html += '<tr>';
            html += '<td><div class="cell-title">' + escapeHtml(row.investment_name || '—') + '</div>' + (row.description ? '<div class="cell-sub truncate">' + escapeHtml(row.description) + '</div>' : '') + '</td>';
            html += '<td><span class="pill ' + (typePills[row.investment_type] || 'pill-neutral') + '">' + escapeHtml(row.investment_type) + '</span></td>';
            html += '<td class="text-end amount">' + formatCurrency(invested) + '</td>';
            html += '<td class="text-end amount">' + (hasCurrent ? formatCurrency(current) : '<span class="text-muted fw-normal">Not set</span>') + '</td>';
            html += '<td class="text-end amount ' + glCls + '">' + (gl === null ? '—' : (gl >= 0 ? '+' : '') + formatCurrency(gl) + '<div class="cell-sub">' + (pct >= 0 ? '+' : '') + pct.toFixed(2) + '%</div>') + '</td>';
            html += '<td>' + escapeHtml(row.account_name || '—') + '</td>';
            html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(row.investment_date)) + '</td>';
            html += '<td class="text-end"><div class="row-actions">';
            html += '<button class="btn-icon btnEdit" data-id="' + row.investment_id + '" title="Edit / update value" aria-label="Edit"><i class="bi bi-pencil"></i></button>';
            html += '<button class="btn-icon danger btnDelete" data-id="' + row.investment_id + '" title="Delete" aria-label="Delete"><i class="bi bi-trash"></i></button>';
            html += '</div></td></tr>';
        });
        $("#investmentTable").html(html);
    }

    function openForm(row) {
        $("#invForm")[0].reset();
        $("#inv_type_custom").hide().val('');
        $("#inv_id").val(row ? row.investment_id : '');
        if (row) {
            $("#inv_name").val(row.investment_name || '');
            setDropdownValue("inv_type_select", "inv_type_custom", row.investment_type);
            $("#inv_amount").val(row.amount);
            $("#inv_current").val(row.current_value !== null ? row.current_value : '');
            $("#inv_account").val(row.account_id);
            $("#inv_date").val(row.investment_date);
            $("#inv_description").val(row.description || '');
        } else {
            $("#inv_date").val(todayISO());
        }
        $("#invModalTitle").text(row ? "Edit investment" : "Add investment");
        $("#invSave").text(row ? "Save changes" : "Add investment");
        modal.show();
    }

    $("#invModal").on("shown.bs.modal", function() { $("#inv_name").focus(); });
    $("#btnAdd").on("click", function() { openForm(null); });
    $(document).on("click", ".btnEdit", function() { openForm(rowsById[$(this).data("id")]); });

    $("#invForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");

        var type = getDropdownValue("inv_type_select", "inv_type_custom");
        var amount = parseFloat($("#inv_amount").val());
        var current = $("#inv_current").val();
        var invalid = [];
        if (!$("#inv_name").val().trim()) invalid.push("#inv_name");
        if (!type) invalid.push($("#inv_type_select").val() === "Other" ? "#inv_type_custom" : "#inv_type_select");
        if (!(amount > 0)) invalid.push("#inv_amount");
        if (current !== "" && parseFloat(current) < 0) invalid.push("#inv_current");
        if (!$("#inv_account").val()) invalid.push("#inv_account");
        if (!$("#inv_date").val()) invalid.push("#inv_date");
        if (invalid.length) {
            $(invalid.join(",")).addClass("is-invalid");
            $(invalid[0]).focus();
            toast("danger", "Please complete the highlighted fields.");
            return;
        }

        var id = $("#inv_id").val();
        var data = {
            module: "investment",
            action: id ? "edit" : "add",
            account_id: $("#inv_account").val(),
            investment_type: type,
            investment_name: $("#inv_name").val().trim(),
            amount: amount,
            current_value: current,
            description: $("#inv_description").val().trim(),
            investment_date: $("#inv_date").val()
        };
        if (id) data.investment_id = id;

        var $form = $(this);
        setFormLoading($form, true);
        ajaxPost(URL, data, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                modal.hide();
                toast("success", response.message);
                $("#searchKeyword").val("").closest(".search-input").removeClass("has-value");
                loadData();
                loadSummary();
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setFormLoading($form, false);
            toast("danger", msg);
        });
    });

    $(document).on("click", ".btnDelete", function() {
        var row = rowsById[$(this).data("id")];
        confirmDialog({
            title: "Delete this investment?",
            message: (row.investment_name || row.investment_type) + " · " + formatCurrency(row.amount) + ". This can't be undone."
        }, function() {
            ajaxPost(URL, { module: "investment", action: "delete", investment_id: row.investment_id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") { loadData($("#searchKeyword").val().trim()); loadSummary(); }
            });
        });
    });
});
