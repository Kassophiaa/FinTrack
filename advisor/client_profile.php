<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/AdvisorPermission.php';

$active = "clients";

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : "";

$title = "Client Profile";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">
        <h4 class="mb-4">Client Profile</h4>

        <div id="alert"></div>

        <a href="clients.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Clients</a>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Full Name:</strong> <span id="clientName"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Username:</strong> <span id="clientUsername"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Email:</strong> <span id="clientEmail"></span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <strong>Phone:</strong> <span id="clientPhone"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-journal-text"></i> Send Advice</div>
            <div class="card-body">
                <form id="adviceForm">
                    <input type="hidden" id="user_id" value="<?php echo $user_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="adviceTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" id="adviceContent" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-send"></i> Send Advice</button>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="clientTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#overviewTab">Overview</a>
            </li>
            <li class="nav-item" id="incomeNavItem" style="display:none;">
                <a class="nav-link" data-bs-toggle="tab" href="#incomeTab"><i class="bi bi-arrow-down-circle"></i> Income</a>
            </li>
            <li class="nav-item" id="expenseNavItem" style="display:none;">
                <a class="nav-link" data-bs-toggle="tab" href="#expenseTab"><i class="bi bi-arrow-up-circle"></i> Expenses</a>
            </li>
            <li class="nav-item" id="investmentNavItem" style="display:none;">
                <a class="nav-link" data-bs-toggle="tab" href="#investmentTab"><i class="bi bi-graph-up"></i> Investments</a>
            </li>
            <li class="nav-item" id="bankNavItem" style="display:none;">
                <a class="nav-link" data-bs-toggle="tab" href="#bankTab"><i class="bi bi-bank"></i> Bank Accounts</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="overviewTab">
                <div class="row mb-4">
                    <div class="col-md-4" id="incomeCardCol">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h6><i class="bi bi-arrow-down-circle"></i> Total Income</h6>
                                <h3 id="totalIncome">&#8369;0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="expenseCardCol">
                        <div class="card text-white bg-danger">
                            <div class="card-body text-center">
                                <h6><i class="bi bi-arrow-up-circle"></i> Total Expense</h6>
                                <h3 id="totalExpense">&#8369;0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="investmentCardCol">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h6><i class="bi bi-graph-up"></i> Total Investment</h6>
                                <h3 id="totalInvestment">&#8369;0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="incomeTab">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="incomeTable"></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="expenseTab">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="expenseTable"></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="investmentTab">
                <div class="row mb-4" id="investmentSummaryCards">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                <h6>Total Invested</h6>
                                <h4 id="invTotalInvested">&#8369;0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h6>Current Value</h6>
                                <h4 id="invTotalCurrent">&#8369;0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white" id="gainLossCard">
                            <div class="card-body text-center">
                                <h6>Gain / Loss</h6>
                                <h4 id="invGainLoss">&#8369;0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white" id="returnCard">
                            <div class="card-body text-center">
                                <h6>Return</h6>
                                <h4 id="invReturn">0.00%</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Account</th>
                                <th>Amount</th>
                                <th>Current Value</th>
                                <th>Gain / Loss</th>
                                <th>Return %</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="investmentTable"></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="bankTab">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Account Name</th>
                                <th>Type</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody id="bankTable"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var user_id = <?php echo $user_id ? intval($user_id) : 0; ?>;
    var loadedTabs = {};
    var currentPermissions = null;

    if (user_id > 0) {
        loadClient();
    }

    function loadClient() {
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "view_client", user_id: user_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    var client = response.client;
                    $("#clientName").text(client.full_name);
                    $("#clientUsername").text(client.username);
                    $("#clientEmail").text(client.email);
                    $("#clientPhone").text(client.phone);

                    var p = response.permissions;
                    currentPermissions = p;
                    var hasIncome = p && p.view_income == 1;
                    var hasExpenses = p && p.view_expenses == 1;
                    var hasInvestments = p && p.view_investments == 1;
                    var hasBankAccounts = p && p.view_bank_accounts == 1;

                    if (hasIncome) {
                        $("#totalIncome").html("&#8369;" + parseFloat(response.total_income).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        $("#incomeNavItem").show();
                    } else {
                        $("#incomeCardCol").find("h3").html('<small style="font-size:0.7em;"><i class="bi bi-lock"></i> Not Shared</small>');
                        $("#incomeNavItem").show().find("a").append(' <i class="bi bi-lock-fill text-muted"></i>');
                    }

                    if (hasExpenses) {
                        $("#totalExpense").html("&#8369;" + parseFloat(response.total_expense).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        $("#expenseNavItem").show();
                    } else {
                        $("#expenseCardCol").find("h3").html('<small style="font-size:0.7em;"><i class="bi bi-lock"></i> Not Shared</small>');
                        $("#expenseNavItem").show().find("a").append(' <i class="bi bi-lock-fill text-muted"></i>');
                    }

                    if (hasInvestments) {
                        $("#totalInvestment").html("&#8369;" + parseFloat(response.total_investment).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        $("#investmentNavItem").show();
                    } else {
                        $("#investmentCardCol").find("h3").html('<small style="font-size:0.7em;"><i class="bi bi-lock"></i> Not Shared</small>');
                        $("#investmentNavItem").show().find("a").append(' <i class="bi bi-lock-fill text-muted"></i>');
                    }

                    if (hasBankAccounts) {
                        $("#bankNavItem").show();
                    } else {
                        $("#bankNavItem").show().find("a").append(' <i class="bi bi-lock-fill text-muted"></i>');
                    }

                    if (p && p.can_give_advice != 1) {
                        $("form#adviceForm").hide();
                    }
                } else {
                    showAlert("#alert", "danger", response.message || "Failed to load client profile.");
                }
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load client profile. Please try again.");
            }
        });
    }

    function loadIncome() {
        if (loadedTabs.income) return;
        loadedTabs.income = true;

        showLoading("#incomeTable", "Loading...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "view_client_income", user_id: user_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "error") {
                    $("#incomeTable").html('<tr><td colspan="4" class="text-center text-muted"><i class="bi bi-lock"></i> ' + escapeHtml(response.message) + '</td></tr>');
                    return;
                }
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.income_type) + '</td>';
                        html += '<td>&#8369;' + parseFloat(row.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                        html += '<td>' + escapeHtml(row.description) + '</td>';
                        html += '<td>' + escapeHtml(row.income_date) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center text-muted">No income records</td></tr>';
                }
                $("#incomeTable").html(html);
            },
            error: function() {
                $("#incomeTable").html('<tr><td colspan="4" class="text-center text-danger">Failed to load income data</td></tr>');
            }
        });
    }

    function loadExpense() {
        if (loadedTabs.expense) return;
        loadedTabs.expense = true;

        showLoading("#expenseTable", "Loading...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "view_client_expense", user_id: user_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "error") {
                    $("#expenseTable").html('<tr><td colspan="4" class="text-center text-muted"><i class="bi bi-lock"></i> ' + escapeHtml(response.message) + '</td></tr>');
                    return;
                }
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.expense_type) + '</td>';
                        html += '<td>&#8369;' + parseFloat(row.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                        html += '<td>' + escapeHtml(row.description) + '</td>';
                        html += '<td>' + escapeHtml(row.expense_date) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center text-muted">No expense records</td></tr>';
                }
                $("#expenseTable").html(html);
            },
            error: function() {
                $("#expenseTable").html('<tr><td colspan="4" class="text-center text-danger">Failed to load expense data</td></tr>');
            }
        });
    }

    function loadInvestmentSummary() {
        if (loadedTabs.investmentSummary) return;
        loadedTabs.investmentSummary = true;

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "view_client_investment_summary", user_id: user_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    var d = response.data;
                    $("#invTotalInvested").html("&#8369;" + d.total_invested.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $("#invTotalCurrent").html("&#8369;" + d.total_current_value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    var glText = (d.total_gain_loss >= 0 ? "+" : "") + "&#8369;" + Math.abs(d.total_gain_loss).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    $("#invGainLoss").html(glText);
                    $("#gainLossCard").removeClass("bg-success bg-danger").addClass(d.total_gain_loss >= 0 ? "bg-success" : "bg-danger");

                    var retText = (d.return_pct >= 0 ? "+" : "") + d.return_pct.toFixed(2) + "%";
                    $("#invReturn").html(retText);
                    $("#returnCard").removeClass("bg-success bg-danger").addClass(d.return_pct >= 0 ? "bg-success" : "bg-danger");
                }
            },
            error: function() {}
        });
    }

    function loadInvestment() {
        if (loadedTabs.investment) return;
        loadedTabs.investment = true;
        loadInvestmentSummary();

        showLoading("#investmentTable", "Loading...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "view_client_investment", user_id: user_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "error") {
                    $("#investmentTable").html('<tr><td colspan="9" class="text-center text-muted"><i class="bi bi-lock"></i> ' + escapeHtml(response.message) + '</td></tr>');
                    return;
                }
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        var gainLoss = 0;
                        var returnPct = 0;
                        if (row.current_value !== null && row.current_value !== "" && parseFloat(row.amount) > 0) {
                            gainLoss = parseFloat(row.current_value) - parseFloat(row.amount);
                            returnPct = (gainLoss / parseFloat(row.amount)) * 100;
                        }

                        var glText = row.current_value !== null && row.current_value !== "" ? (gainLoss >= 0 ? "+" : "") + "&#8369;" + Math.abs(gainLoss).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : "&#8212;";
                        var retText = row.current_value !== null && row.current_value !== "" ? (returnPct >= 0 ? "+" : "") + returnPct.toFixed(2) + "%" : "&#8212;";
                        var glClass = row.current_value !== null && row.current_value !== "" ? (gainLoss >= 0 ? "text-success" : "text-danger") : "";
                        var cvText = row.current_value !== null && row.current_value !== "" ? "&#8369;" + parseFloat(row.current_value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : "Not updated";

                        html += '<tr>';
                        html += '<td><span class="badge bg-primary">' + escapeHtml(row.investment_type) + '</span></td>';
                        html += '<td>' + escapeHtml(row.investment_name || '&#8212;') + '</td>';
                        html += '<td>' + escapeHtml(row.account_name || '&#8212;') + '</td>';
                        html += '<td>&#8369;' + parseFloat(row.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                        html += '<td>' + cvText + '</td>';
                        html += '<td class="' + glClass + '">' + glText + '</td>';
                        html += '<td class="' + glClass + '">' + retText + '</td>';
                        html += '<td>' + escapeHtml(row.description) + '</td>';
                        html += '<td>' + escapeHtml(row.investment_date) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="9" class="text-center text-muted">No investment records</td></tr>';
                }
                $("#investmentTable").html(html);
            },
            error: function() {
                $("#investmentTable").html('<tr><td colspan="9" class="text-center text-danger">Failed to load investment data</td></tr>');
            }
        });
    }

    function loadBankAccounts() {
        if (loadedTabs.bank) return;
        loadedTabs.bank = true;

        showLoading("#bankTable", "Loading...");
        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: { action: "view_client_bank_accounts", user_id: user_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "error") {
                    $("#bankTable").html('<tr><td colspan="3" class="text-center text-muted"><i class="bi bi-lock"></i> ' + escapeHtml(response.message) + '</td></tr>');
                    return;
                }
                var html = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr>';
                        html += '<td>' + escapeHtml(row.account_name) + '</td>';
                        html += '<td>' + escapeHtml(row.account_type) + '</td>';
                        html += '<td>&#8369;' + parseFloat(row.balance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted">No bank accounts</td></tr>';
                }
                $("#bankTable").html(html);
            },
            error: function() {
                $("#bankTable").html('<tr><td colspan="3" class="text-center text-danger">Failed to load bank account data</td></tr>');
            }
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return "&#8212;";
        var parts = dateStr.split("-");
        if (parts.length !== 3) return dateStr;
        return parts[0] + "-" + parts[1].padStart(2, "0") + "-" + parts[2].padStart(2, "0");
    }

    $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function(e) {
        var target = $(e.target).attr("href");
        if (target === "#incomeTab") {
            loadIncome();
        } else if (target === "#expenseTab") {
            loadExpense();
        } else if (target === "#investmentTab") {
            loadInvestment();
        } else if (target === "#bankTab") {
            loadBankAccounts();
        }
    });

    $("#adviceForm").on("submit", function(e) {
        e.preventDefault();

        var title = $("#adviceTitle").val();
        var content = $("#adviceContent").val();
        var $form = $(this);
        var origText = $form.find('button[type="submit"]').html();
        setFormLoading($form, true);

        $.ajax({
            url: "<?php echo BASE_URL; ?>/db/advisor_request.php",
            type: "POST",
            data: {
                action: "add_advice",
                user_id: user_id,
                title: title,
                content: content
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    $form[0].reset();
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

});
</script>
