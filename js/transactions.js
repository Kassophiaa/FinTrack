// Shared logic for the Income and Expenses pages (configured by the global TX object)
$(document).ready(function() {

    var URL = BASE_URL + "/db/request.php";
    var rowsById = {};
    var modal = new bootstrap.Modal(document.getElementById("txModal"));

    initDropdownCustom("tx_type_select", "tx_type_custom");
    loadData();
    bindSearch("#searchKeyword", function(keyword) { loadData(keyword); });

    function loadData(keyword) {
        showLoading("#txTable", "Loading...");
        var data = keyword ? { module: TX.module, action: "search", keyword: keyword } : { module: TX.module, action: "display" };
        ajaxPost(URL, data, function(response) {
            var rows = response.data || [];
            renderTable(rows, !!keyword);
            if (!keyword) updateStats(rows);
        }, function(msg) {
            $("#txTable").html(emptyRow(6, "bi-wifi-off", "Couldn't load records", msg));
        });
    }

    function updateStats(rows) {
        var thisMonth = todayISO().substring(0, 7);
        var total = 0, month = 0;
        rows.forEach(function(r) {
            var amt = parseFloat(r.amount) || 0;
            total += amt;
            if (String(r[TX.dateField]).substring(0, 7) === thisMonth) month += amt;
        });
        $("#statMonth").text(formatCurrency(month));
        $("#statTotal").text(formatCurrency(total));
        $("#statCount").text(rows.length);
    }

    function renderTable(rows, isSearch) {
        rowsById = {};
        if (!rows.length) {
            var action = (!isSearch && TX.hasAccounts) ? '<button class="btn btn-primary btn-sm" onclick="$(\'#btnAdd\').click()"><i class="bi bi-plus-lg"></i> Add ' + escapeHtml(TX.noun) + '</button>' : '';
            $("#txTable").html(isSearch
                ? emptyRow(6, "bi-search", "No matching records", "Try a different keyword.")
                : emptyRow(6, "bi-receipt", "No " + TX.noun + " recorded yet", "Records you add will show up here.", action));
            $("#txSummary").hide();
            return;
        }

        var html = "", total = 0;
        rows.forEach(function(row) {
            var id = row[TX.idField];
            rowsById[id] = row;
            total += parseFloat(row.amount) || 0;
            html += '<tr>';
            html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(row[TX.dateField])) + '</td>';
            html += '<td><span class="pill pill-neutral">' + escapeHtml(row[TX.typeField]) + '</span></td>';
            html += '<td class="truncate text-secondary" title="' + escapeHtml(row.description || '') + '">' + (row.description ? escapeHtml(row.description) : '<span class="text-muted">—</span>') + '</td>';
            html += '<td>' + escapeHtml(row.account_name || '—') + '</td>';
            html += '<td class="text-end amount ' + (TX.sign === '+' ? 'text-success' : '') + '">' + TX.sign + formatCurrency(row.amount) + '</td>';
            html += '<td class="text-end"><div class="row-actions">';
            html += '<button class="btn-icon btnEdit" data-id="' + escapeHtml(id) + '" title="Edit" aria-label="Edit"><i class="bi bi-pencil"></i></button>';
            html += '<button class="btn-icon danger btnDelete" data-id="' + escapeHtml(id) + '" title="Delete" aria-label="Delete"><i class="bi bi-trash"></i></button>';
            html += '</div></td>';
            html += '</tr>';
        });
        $("#txTable").html(html);
        $("#txSummary").html('<span>' + rows.length + ' record' + (rows.length === 1 ? '' : 's') + '</span><span>Total: <strong>' + formatCurrency(total) + '</strong></span>').show();
    }

    function openForm(row) {
        $("#txForm")[0].reset();
        $("#tx_type_custom").hide().val('');
        if (row) {
            $("#txModalTitle").text("Edit " + TX.noun);
            $("#tx_id").val(row[TX.idField]);
            $("#tx_amount").val(row.amount);
            $("#tx_date").val(row[TX.dateField]);
            setDropdownValue("tx_type_select", "tx_type_custom", row[TX.typeField]);
            $("#tx_account").val(row.account_id);
            $("#tx_description").val(row.description || '');
            $("#txSave").text("Save changes");
        } else {
            $("#txModalTitle").text("Add " + TX.noun);
            $("#tx_id").val('');
            $("#tx_date").val(todayISO());
            $("#txSave").text("Add " + TX.noun);
        }
        modal.show();
    }

    $("#txModal").on("shown.bs.modal", function() { $("#tx_amount").focus(); });

    $("#btnAdd").on("click", function() { openForm(null); });

    $(document).on("click", ".btnEdit", function() {
        openForm(rowsById[$(this).data("id")]);
    });

    $("#txForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");

        var type = getDropdownValue("tx_type_select", "tx_type_custom");
        var amount = parseFloat($("#tx_amount").val());
        var invalid = [];
        if (!(amount > 0)) invalid.push("#tx_amount");
        if (!$("#tx_date").val()) invalid.push("#tx_date");
        if (!type) invalid.push($("#tx_type_select").val() === "Other" ? "#tx_type_custom" : "#tx_type_select");
        if (!$("#tx_account").val()) invalid.push("#tx_account");
        if (invalid.length) {
            $(invalid.join(",")).addClass("is-invalid");
            $(invalid[0]).focus();
            toast("danger", "Please complete the highlighted fields.");
            return;
        }

        var id = $("#tx_id").val();
        var data = { module: TX.module, action: id ? "edit" : "add", account_id: $("#tx_account").val(), amount: amount, description: $("#tx_description").val().trim() };
        data[TX.typeField] = type;
        data[TX.dateField] = $("#tx_date").val();
        if (id) data[TX.idField] = id;

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
        var id = $(this).data("id");
        var row = rowsById[id];
        var $btn = $(this);
        confirmDialog({
            title: "Delete this " + TX.noun + "?",
            message: row[TX.typeField] + " · " + formatCurrency(row.amount) + " on " + formatDate(row[TX.dateField]) + ". This can't be undone."
        }, function() {
            setButtonLoading($btn, true);
            var data = { module: TX.module, action: "delete" };
            data[TX.idField] = id;
            ajaxPost(URL, data, function(response) {
                if (response.status == "success") {
                    toast("success", response.message);
                    loadData($("#searchKeyword").val().trim());
                    if ($("#searchKeyword").val().trim()) loadStatsOnly();
                } else {
                    setButtonLoading($btn, false);
                    toast("danger", response.message);
                }
            }, function(msg) {
                setButtonLoading($btn, false);
                toast("danger", msg);
            });
        });
    });

    function loadStatsOnly() {
        ajaxPost(URL, { module: TX.module, action: "display" }, function(response) { updateStats(response.data || []); });
    }
});
