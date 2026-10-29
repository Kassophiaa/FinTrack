$(document).ready(function() {

    initDropdownCustom("account_type_select", "account_type_custom");
    initDropdownCustom("edit_account_type_select", "edit_account_type_custom");

    loadData();

    function loadData() {
        showLoading("#accountTable", "Loading accounts...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "account", action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                renderTable(response.data);
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load accounts. Please try again.");
                $("#accountTable").html('<tr><td colspan="5" class="text-center text-muted">Failed to load data</td></tr>');
            }
        });
    }

    function renderTable(data) {
        var html = "";
        if (data && data.length > 0) {
            $.each(data, function(i, row) {
                html += '<tr>';
                html += '<td>' + escapeHtml(row.account_id) + '</td>';
                html += '<td>' + escapeHtml(row.account_name) + '</td>';
                html += '<td>' + escapeHtml(row.account_type) + '</td>';
                html += '<td>' + formatCurrency(row.balance) + '</td>';
                html += '<td>';
                html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.account_id + '" data-name="' + escapeHtml(row.account_name) + '" data-type="' + escapeHtml(row.account_type) + '" data-balance="' + row.balance + '" title="Edit"><i class="bi bi-pencil"></i></button> ';
                html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.account_id + '" title="Delete"><i class="bi bi-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:1.5rem;"></i><br>No accounts found</td></tr>';
        }
        $("#accountTable").html(html);
    }

    $("#accountForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var origText = $form.find('button[type="submit"]').html();

        var accountType = getDropdownValue("account_type_select", "account_type_custom");
        if (!accountType) {
            showAlert("#alert", "danger", "Please select or enter an account type.");
            return;
        }

        setFormLoading($form, true);

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: {
                module: "account",
                action: "add",
                account_name: $("#account_name").val(),
                account_type: accountType,
                balance: $("#balance").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $form[0].reset();
                    $("#account_type_custom").hide().val('');
                    loadData();
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

        showLoading("#accountTable", "Searching...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "account", action: "search", keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                renderTable(response.data);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
                $("#accountTable").html('<tr><td colspan="5" class="text-center text-muted">Search failed</td></tr>');
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
        $("#edit_account_id").val($(this).data("id"));
        $("#edit_account_name").val($(this).data("name"));
        setDropdownValue("edit_account_type_select", "edit_account_type_custom", $(this).data("type"));
        $("#edit_balance").val($(this).data("balance"));
        $("#editModal").modal("show");
    });

    $("#btnSaveEdit").on("click", function() {
        var $btn = $(this);
        var origText = $btn.html();

        var accountType = getDropdownValue("edit_account_type_select", "edit_account_type_custom");
        if (!accountType) {
            showAlert("#alert", "danger", "Please select or enter an account type.");
            return;
        }

        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: {
                module: "account",
                action: "edit",
                account_id: $("#edit_account_id").val(),
                account_name: $("#edit_account_name").val(),
                account_type: accountType,
                balance: $("#edit_balance").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $("#editModal").modal("hide");
                    loadData();
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
        if (!confirm("Are you sure you want to delete this account?")) return;

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "account", action: "delete", account_id: $(this).data("id") },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    loadData();
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
