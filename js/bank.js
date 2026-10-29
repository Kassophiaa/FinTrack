$(document).ready(function() {

    var URL = BASE_URL + "/db/request.php";
    var rowsById = {};
    var modal = new bootstrap.Modal(document.getElementById("accountModal"));
    var typeIcons = { 'Savings': 'bi-piggy-bank', 'Checking': 'bi-bank', 'Credit Card': 'bi-credit-card', 'E-Wallet': 'bi-phone', 'Cash': 'bi-cash-stack' };

    initDropdownCustom("acc_type_select", "acc_type_custom");
    loadData();
    bindSearch("#searchKeyword", function(keyword) { loadData(keyword); });

    function loadData(keyword) {
        showCardLoading("#accountList", "Loading accounts...");
        var data = keyword ? { module: "account", action: "search", keyword: keyword } : { module: "account", action: "display" };
        ajaxPost(URL, data, function(response) {
            var rows = response.data || [];
            render(rows, !!keyword);
            if (!keyword) {
                var total = rows.reduce(function(sum, r) { return sum + (parseFloat(r.balance) || 0); }, 0);
                $("#statTotal").text(formatCurrency(total));
                $("#statCount").text(rows.length + " account" + (rows.length === 1 ? "" : "s"));
            }
        }, function(msg) {
            $("#accountList").html('<div class="col-12"><div class="card">' + emptyStateHtml("bi-wifi-off", "Couldn't load accounts", msg) + '</div></div>');
        });
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
            html += '<div class="col-sm-6 col-xl-4"><div class="card h-100"><div class="card-body d-flex flex-column gap-3">';
            html += '<div class="d-flex align-items-start gap-3"><span class="icon-chip lg chip-primary"><i class="bi ' + icon + '"></i></span>';
            html += '<div class="flex-grow-1" style="min-width:0;"><div class="fw-bold text-truncate">' + escapeHtml(row.account_name) + '</div><span class="pill pill-neutral mt-1">' + escapeHtml(row.account_type) + '</span></div>';
            html += '<div class="row-actions"><button class="btn-icon btnEdit" data-id="' + row.account_id + '" title="Edit" aria-label="Edit"><i class="bi bi-pencil"></i></button>';
            html += '<button class="btn-icon danger btnDelete" data-id="' + row.account_id + '" title="Delete" aria-label="Delete"><i class="bi bi-trash"></i></button></div></div>';
            html += '<div><div class="small-label">Balance</div><div class="stat-value">' + formatCurrency(row.balance) + '</div></div>';
            html += '</div></div></div>';
        });
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
            message: "The account will be removed from your list. Transactions already recorded to it are kept."
        }, function() {
            ajaxPost(URL, { module: "account", action: "delete", account_id: row.account_id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") loadData();
            });
        });
    });
});
