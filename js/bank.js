$(document).ready(function() {

    var URL = BASE_URL + "/db/request.php";
    var rowsById = {};
    var modal = new bootstrap.Modal(document.getElementById("accountModal"));
    var typeIcons = { 'Savings': 'bi-piggy-bank', 'Checking': 'bi-bank', 'Credit Card': 'bi-credit-card', 'E-Wallet': 'bi-phone', 'Cash': 'bi-cash-stack' };

    initDropdownCustom("acc_type_select", "acc_type_custom");
    loadData();
    bindSearch("#searchKeyword", function(keyword) { loadData(keyword); });

    // Categorical palette (fixed order, validated); accounts past the 8th fold into "Other"
    var PALETTE = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#6250d6', '#e34948'];
    var OTHER_COLOR = '#9aa5a0';
    var colorById = {};
    var grandTotal = 0;

    function loadData(keyword) {
        showCardLoading("#accountList", "Loading accounts...");
        var data = keyword ? { module: "account", action: "search", keyword: keyword } : { module: "account", action: "display" };
        ajaxPost(URL, data, function(response) {
            var rows = response.data || [];
            if (!keyword) renderSummary(rows);
            render(rows, !!keyword);
        }, function(msg) {
            $("#accountList").html('<div class="col-12"><div class="card">' + emptyStateHtml("bi-wifi-off", "Couldn't load accounts", msg) + '</div></div>');
        });
    }

    // Summary uses the full (unfiltered) list and fixes each account's color
    function renderSummary(rows) {
        colorById = {};
        var fold = rows.length > PALETTE.length;
        rows.forEach(function(r, i) {
            colorById[r.account_id] = (!fold || i < PALETTE.length - 1) ? PALETTE[i] : OTHER_COLOR;
        });

        grandTotal = rows.reduce(function(sum, r) { return sum + (parseFloat(r.balance) || 0); }, 0);
        $("#statTotal").text(formatCurrency(grandTotal));
        $("#statCount").text(rows.length + " account" + (rows.length === 1 ? "" : "s"));
        $("#listTitle").text(rows.length ? "Your accounts (" + rows.length + ")" : "Your accounts");
        $("#summaryCard").toggle(rows.length > 0);

        if (grandTotal <= 0) {
            $("#allocBar").html("");
            $("#allocLegend").html('<span class="text-muted">Add balances to see how your money is split.</span>');
            return;
        }

        // Build segments: named accounts, then one "Other" bucket if folded
        var segments = [], other = 0;
        rows.forEach(function(r) {
            var bal = parseFloat(r.balance) || 0;
            if (colorById[r.account_id] === OTHER_COLOR) { other += bal; return; }
            segments.push({ name: r.account_name, value: bal, color: colorById[r.account_id] });
        });
        if (other > 0) segments.push({ name: "Other", value: other, color: OTHER_COLOR });

        var bar = "", legend = "";
        segments.forEach(function(s) {
            var pct = s.value / grandTotal * 100;
            var label = s.name + ": " + formatCurrency(s.value) + " (" + pct.toFixed(1) + "%)";
            if (s.value > 0) bar += '<div class="alloc-seg" style="flex:' + s.value + ' 0 0; background:' + s.color + ';" title="' + escapeHtml(label) + '"></div>';
            legend += '<span class="legend-item"><i class="swatch-dot" style="background:' + s.color + ';"></i>' + escapeHtml(s.name) + ' <strong>' + pct.toFixed(0) + '%</strong></span>';
        });
        $("#allocBar").html(bar);
        $("#allocLegend").html(legend);
    }

    function render(rows, isSearch) {
        rowsById = {};
        if (!rows.length) {
            $("#accountList").html('<div class="col-12"><div class="card">' + (isSearch
                ? emptyStateHtml("bi-search", "No matching accounts", "Try a different keyword.")
                : emptyStateHtml("bi-bank", "No accounts yet", "Add your first bank account, e-wallet or cash wallet to start tracking.", '<button class="btn btn-primary btn-sm" onclick="$(\'#btnAdd\').click()"><i class="bi bi-plus-lg"></i> Add account</button>')) + '</div></div>');
            return;
        }
        var html = "";
        rows.forEach(function(row) {
            rowsById[row.account_id] = row;
            var icon = typeIcons[row.account_type] || 'bi-wallet2';
            var color = colorById[row.account_id] || OTHER_COLOR;
            var bal = parseFloat(row.balance) || 0;
            var share = grandTotal > 0 ? bal / grandTotal * 100 : 0;
            var monthIn = parseFloat(row.month_in) || 0, monthOut = parseFloat(row.month_out) || 0;
            var txCount = parseInt(row.tx_count, 10) || 0;

            html += '<div class="col-md-6 col-xl-4"><div class="card account-card">';
            html += '<div class="account-head">';
            html += '<span class="icon-chip lg chip-primary"><i class="bi ' + icon + '"></i></span>';
            html += '<div style="min-width:0;" class="flex-grow-1"><div class="account-name" title="' + escapeHtml(row.account_name) + '">' + escapeHtml(row.account_name) + '</div>';
            html += '<div class="account-type"><i class="swatch-dot" style="background:' + color + ';"></i>' + escapeHtml(row.account_type) + '</div></div>';
            html += '<div class="row-actions"><button class="btn-icon btnEdit" data-id="' + row.account_id + '" title="Edit account" aria-label="Edit ' + escapeHtml(row.account_name) + '"><i class="bi bi-pencil"></i></button>';
            html += '<button class="btn-icon danger btnDelete" data-id="' + row.account_id + '" title="Delete account" aria-label="Delete ' + escapeHtml(row.account_name) + '"><i class="bi bi-trash"></i></button></div>';
            html += '</div>';

            html += '<div class="account-balance"><div class="amount-lg">' + formatCurrency(bal) + '</div>';
            html += '<div class="d-flex justify-content-between small text-muted mt-1"><span>' + share.toFixed(1) + '% of total</span><span>' + txCount + ' transaction' + (txCount === 1 ? '' : 's') + '</span></div>';
            html += '<div class="share-track"><div class="share-fill" style="width:' + share.toFixed(2) + '%; background:' + color + ';"></div></div></div>';

            html += '<div class="account-foot">';
            if (monthIn === 0 && monthOut === 0) {
                html += '<div style="grid-column: span 2;" class="text-muted"><i class="bi bi-calendar3 me-1"></i>No activity this month</div>';
            } else {
                html += '<div><span class="foot-label"><i class="bi bi-arrow-down-left"></i> In this month</span><span class="foot-value text-success">+' + formatCurrency(monthIn) + '</span></div>';
                html += '<div><span class="foot-label"><i class="bi bi-arrow-up-right"></i> Out this month</span><span class="foot-value">-' + formatCurrency(monthOut) + '</span></div>';
            }
            html += '</div></div></div>';
        });
        if (!isSearch) {
            html += '<div class="col-md-6 col-xl-4"><button type="button" class="add-card" onclick="$(\'#btnAdd\').click()"><span class="add-icon"><i class="bi bi-plus-lg"></i></span>Add another account<span class="small text-muted fw-normal">Bank, e-wallet, credit card or cash</span></button></div>';
        }
        $("#accountList").html(html);
    }

    function openForm(row) {
        $("#accountForm")[0].reset();
        $("#acc_type_custom").hide().val('');
        $("#acc_id").val(row ? row.account_id : '');
        if (row) {
            $("#acc_name").val(row.account_name);
            setDropdownValue("acc_type_select", "acc_type_custom", row.account_type);
            $("#acc_balance").val(row.balance);
        }
        $("#accountModalTitle").text(row ? "Edit account" : "Add account");
        $("#accSave").text(row ? "Save changes" : "Add account");
        modal.show();
    }

    $("#accountModal").on("shown.bs.modal", function() { $("#acc_name").focus(); });
    $("#btnAdd").on("click", function() { openForm(null); });
    $(document).on("click", ".btnEdit", function() { openForm(rowsById[$(this).data("id")]); });

    $("#accountForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");

        var type = getDropdownValue("acc_type_select", "acc_type_custom");
        var name = $("#acc_name").val().trim();
        var balanceStr = $("#acc_balance").val();
        var invalid = [];
        if (!name) invalid.push("#acc_name");
        if (!type) invalid.push($("#acc_type_select").val() === "Other" ? "#acc_type_custom" : "#acc_type_select");
        if (balanceStr === "" || parseFloat(balanceStr) < 0) invalid.push("#acc_balance");
        if (invalid.length) {
            $(invalid.join(",")).addClass("is-invalid");
            $(invalid[0]).focus();
            toast("danger", "Please complete the highlighted fields. Balance can't be negative.");
            return;
        }

        var id = $("#acc_id").val();
        var data = { module: "account", action: id ? "edit" : "add", account_name: name, account_type: type, balance: balanceStr };
        if (id) data.account_id = id;

        var $form = $(this);
        setFormLoading($form, true);
        ajaxPost(URL, data, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                modal.hide();
                toast("success", response.message);
                $("#searchKeyword").val("").closest(".search-input").removeClass("has-value");
                loadData();
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
            title: "Delete " + row.account_name + "?",
            message: "The account will be removed from your list." + (parseInt(row.tx_count, 10) > 0 ? " Its " + row.tx_count + " recorded transaction" + (row.tx_count == 1 ? " is" : "s are") + " kept." : "")
        }, function() {
            ajaxPost(URL, { module: "account", action: "delete", account_id: row.account_id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") loadData();
            });
        });
    });
});
