$(document).ready(function() {

    initDropdownCustom("expense_type_select", "expense_type_custom");
    initDropdownCustom("edit_expense_type_select", "edit_expense_type_custom");

    loadData();

    function loadData() {
        showLoading("#expenseTable", "Loading expense records...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "expense", action: "display" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                renderTable(response.data);
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load expense records. Please try again.");
                $("#expenseTable").html('<tr><td colspan="7" class="text-center text-muted">Failed to load data</td></tr>');
            }
        });
    }

    function renderTable(data) {
        var html = "";
        if (data && data.length > 0) {
            $.each(data, function(i, row) {
                html += '<tr>';
                html += '<td>' + escapeHtml(row.expense_id) + '</td>';
                html += '<td>' + escapeHtml(row.expense_type) + '</td>';
                html += '<td>' + formatCurrency(row.amount) + '</td>';
                html += '<td>' + escapeHtml(row.description || '') + '</td>';
                html += '<td>' + escapeHtml(row.account_name || '-') + '</td>';
                html += '<td>' + escapeHtml(row.expense_date) + '</td>';
                html += '<td>';
                html += '<button class="btn btn-sm btn-warning btnEdit" data-id="' + row.expense_id + '" data-account="' + row.account_id + '" data-type="' + escapeHtml(row.expense_type) + '" data-amount="' + row.amount + '" data-desc="' + escapeHtml(row.description || '') + '" data-date="' + row.expense_date + '" title="Edit"><i class="bi bi-pencil"></i></button> ';
                html += '<button class="btn btn-sm btn-danger btnDelete" data-id="' + row.expense_id + '" title="Delete"><i class="bi bi-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:1.5rem;"></i><br>No expense records found</td></tr>';
        }
        $("#expenseTable").html(html);
    }

    $("#expenseForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var origText = $form.find('button[type="submit"]').html();

        var expenseType = getDropdownValue("expense_type_select", "expense_type_custom");
        if (!expenseType) {
            showAlert("#alert", "danger", "Please select or enter an expense category.");
            return;
        }

        setFormLoading($form, true);

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: {
                module: "expense",
                action: "add",
                account_id: $("#account_id").val(),
                expense_type: expenseType,
                amount: $("#amount").val(),
                description: $("#description").val(),
                expense_date: $("#expense_date").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $form[0].reset();
                    $("#expense_type_custom").hide().val('');
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

        showLoading("#expenseTable", "Searching...");
        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "expense", action: "search", keyword: keyword },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                renderTable(response.data);
            },
            error: function() {
                showAlert("#alert", "danger", "Search failed. Please try again.");
                $("#expenseTable").html('<tr><td colspan="7" class="text-center text-muted">Search failed</td></tr>');
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
        $("#edit_expense_id").val($(this).data("id"));
        $("#edit_account_id").val($(this).data("account"));
        setDropdownValue("edit_expense_type_select", "edit_expense_type_custom", $(this).data("type"));
        $("#edit_amount").val($(this).data("amount"));
        $("#edit_description").val($(this).data("desc"));
        $("#edit_expense_date").val($(this).data("date"));
        $("#editModal").modal("show");
    });

    $("#btnSaveEdit").on("click", function() {
        var $btn = $(this);
        var origText = $btn.html();

        var expenseType = getDropdownValue("edit_expense_type_select", "edit_expense_type_custom");
        if (!expenseType) {
            showAlert("#alert", "danger", "Please select or enter an expense category.");
            return;
        }

        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: {
                module: "expense",
                action: "edit",
                expense_id: $("#edit_expense_id").val(),
                account_id: $("#edit_account_id").val(),
                expense_type: expenseType,
                amount: $("#edit_amount").val(),
                description: $("#edit_description").val(),
                expense_date: $("#edit_expense_date").val()
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
        if (!confirm("Are you sure you want to delete this expense record?")) return;

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/request.php",
            type: "POST",
            data: { module: "expense", action: "delete", expense_id: $(this).data("id") },
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
