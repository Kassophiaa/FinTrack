<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "clients";

$user_id = intval($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
    redirect(ADVISOR_PATH . "/clients.php");
}

$title = "Client Profile";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<a href="clients.php" class="back-link"><i class="bi bi-arrow-left"></i> All clients</a>

<div id="loadError" style="display:none;"></div>

<div id="profileContent">
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center gap-3 flex-wrap">
            <span class="avatar lg" id="clientAvatar">…</span>
            <div class="flex-grow-1" style="min-width: 200px;">
                <h1 class="h4 mb-1" id="clientName">Loading…</h1>
                <div class="text-muted small d-flex gap-3 flex-wrap">
                    <span><i class="bi bi-at"></i><span id="clientUsername"></span></span>
                    <span><i class="bi bi-envelope"></i> <span id="clientEmail"></span></span>
                    <span><i class="bi bi-telephone"></i> <span id="clientPhone"></span></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-light" id="btnMessage" href="messages.php?with=<?php echo $user_id; ?>" style="display:none;"><i class="bi bi-chat-dots"></i> Message</a>
                <a class="btn btn-primary" id="btnWriteAdvice" href="#adviceCard" style="display:none;"><i class="bi bi-journal-plus"></i> Write advice</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card"><div class="stat-top"><span class="stat-label">Total income</span><span class="icon-chip chip-success"><i class="bi bi-arrow-down-left"></i></span></div><div class="stat-value" id="totalIncome">—</div></div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card"><div class="stat-top"><span class="stat-label">Total expenses</span><span class="icon-chip chip-danger"><i class="bi bi-arrow-up-right"></i></span></div><div class="stat-value" id="totalExpense">—</div></div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card"><div class="stat-top"><span class="stat-label">Net savings</span><span class="icon-chip chip-primary"><i class="bi bi-piggy-bank"></i></span></div><div class="stat-value" id="netSavings">—</div></div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card"><div class="stat-top"><span class="stat-label">Total invested</span><span class="icon-chip chip-info"><i class="bi bi-graph-up-arrow"></i></span></div><div class="stat-value" id="totalInvestment">—</div></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card table-card">
                <div class="card-header pb-0 border-0">
                    <ul class="nav nav-tabs w-100" id="clientTabs">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#incomeTab" data-key="income" type="button"><i class="bi bi-arrow-down-left"></i> Income</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#expenseTab" data-key="expense" type="button"><i class="bi bi-arrow-up-right"></i> Expenses</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#investmentTab" data-key="investment" type="button"><i class="bi bi-graph-up-arrow"></i> Investments</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bankTab" data-key="bank" type="button"><i class="bi bi-bank"></i> Accounts</button></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="incomeTab">
                        <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Date</th><th>Type</th><th>Description</th><th class="text-end">Amount</th></tr></thead><tbody id="incomeTable"></tbody></table></div>
                    </div>
                    <div class="tab-pane fade" id="expenseTab">
                        <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Date</th><th>Category</th><th>Description</th><th class="text-end">Amount</th></tr></thead><tbody id="expenseTable"></tbody></table></div>
                    </div>
                    <div class="tab-pane fade" id="investmentTab">
                        <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Investment</th><th>Type</th><th class="text-end">Invested</th><th class="text-end">Current</th><th class="text-end">Gain / loss</th><th>Date</th></tr></thead><tbody id="investmentTable"></tbody></table></div>
                    </div>
                    <div class="tab-pane fade" id="bankTab">
                        <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Account</th><th>Type</th><th class="text-end">Balance</th></tr></thead><tbody id="bankTable"></tbody></table></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-3" id="adviceCard">
                <div class="card-header"><h3 class="card-title"><i class="bi bi-journal-plus"></i> Write advice</h3></div>
                <div class="card-body">
                    <form id="adviceForm" novalidate>
                        <div class="mb-3">
                            <label class="form-label" for="adviceTitle">Title<span class="req">*</span></label>
                            <input type="text" class="form-control" id="adviceTitle" maxlength="100" placeholder="e.g. Build an emergency fund" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="adviceContent">Advice<span class="req">*</span></label>
                            <textarea class="form-control" id="adviceContent" rows="5" maxlength="2000" placeholder="Write clear, actionable recommendations…" required></textarea>
                            <div class="form-text"><span id="adviceCount">0</span>/2000</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> Send advice</button>
                    </form>
                    <div class="notice mb-0" id="adviceLocked" style="display:none;"><i class="bi bi-lock"></i><div>This client hasn't allowed you to send advice.</div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="bi bi-clock-history"></i> Advice history</h3></div>
                <div id="adviceHistory"></div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {

    var URL = BASE_URL + "/db/advisor_request.php";
    var user_id = <?php echo $user_id; ?>;
    var perms = {};
    var loaded = {};
    var permKey = { income: 'view_income', expense: 'view_expenses', investment: 'view_investments', bank: 'view_bank_accounts' };
    var lockedLabel = { income: 'income', expense: 'expenses', investment: 'investments', bank: 'bank accounts' };

    loadClient();
    loadAdviceHistory();

    $("#adviceContent").on("input", function() { $("#adviceCount").text($(this).val().length); });

    function lockedRow(cols, key) {
        return emptyRow(cols, "bi-lock", "Not shared with you", "This client hasn't shared their " + lockedLabel[key] + ".");
    }

    function loadClient() {
        ajaxPost(URL, { action: "view_client", user_id: user_id }, function(response) {
            if (response.status != "success") {
                $("#profileContent").hide();
                $("#loadError").show().html('<div class="card">' + emptyStateHtml("bi-person-x", "Client unavailable", response.message || "You don't have access to this client.", '<a href="clients.php" class="btn btn-primary btn-sm">Back to clients</a>') + '</div>');
                return;
            }
            var c = response.client;
            perms = response.permissions || {};
            $("#clientAvatar").text(initials(c.full_name));
            $("#clientName").text(c.full_name);
            $("#clientUsername").text(c.username);
            $("#clientEmail").text(c.email);
            $("#clientPhone").text(c.phone || "—");
            document.title = c.full_name + " · FinTrack";

            var lock = '<span class="fs-6 text-muted fw-semibold"><i class="bi bi-lock"></i> Not shared</span>';
            $("#totalIncome").html(perms.view_income == 1 ? formatCurrency(response.total_income) : lock);
            $("#totalExpense").html(perms.view_expenses == 1 ? formatCurrency(response.total_expense) : lock);
            $("#totalInvestment").html(perms.view_investments == 1 ? formatCurrency(response.total_investment) : lock);
            if (perms.view_income == 1 && perms.view_expenses == 1) {
                var net = (parseFloat(response.total_income) || 0) - (parseFloat(response.total_expense) || 0);
                $("#netSavings").text(formatCurrency(net)).toggleClass("text-danger", net < 0);
            } else {
                $("#netSavings").html(lock);
            }

            $("#clientTabs .nav-link").each(function() {
                var key = $(this).data("key");
                if (perms[permKey[key]] != 1) $(this).append(' <i class="bi bi-lock-fill text-muted small"></i>');
            });

            $("#btnMessage").toggle(perms.can_message == 1);
            var canAdvise = perms.can_give_advice == 1;
            $("#btnWriteAdvice").toggle(canAdvise);
            $("#adviceForm").toggle(canAdvise);
            $("#adviceLocked").toggle(!canAdvise);

            loadTab("income");
        }, function(msg) {
            toast("danger", msg);
        });
    }

    $('#clientTabs button[data-bs-toggle="tab"]').on("shown.bs.tab", function() {
        loadTab($(this).data("key"));
    });

    function loadTab(key) {
        if (loaded[key]) return;
        loaded[key] = true;
        var tables = { income: ["#incomeTable", 4, "view_client_income"], expense: ["#expenseTable", 4, "view_client_expense"], investment: ["#investmentTable", 6, "view_client_investment"], bank: ["#bankTable", 3, "view_client_bank_accounts"] };
        var t = tables[key];
        if (perms[permKey[key]] != 1) { $(t[0]).html(lockedRow(t[1], key)); return; }

        showLoading(t[0], "Loading...");
        ajaxPost(URL, { action: t[2], user_id: user_id }, function(response) {
            if (response.status != "success") { $(t[0]).html(lockedRow(t[1], key)); return; }
            var rows = response.data || [];
            if (!rows.length) { $(t[0]).html(emptyRow(t[1], "bi-inbox", "Nothing recorded yet", "")); return; }
            var html = "";
            rows.forEach(function(r) {
                if (key === "income" || key === "expense") {
                    html += '<tr><td style="white-space:nowrap;">' + escapeHtml(formatDate(r[key + "_date"])) + '</td><td><span class="pill pill-neutral">' + escapeHtml(r[key + "_type"]) + '</span></td>';
                    html += '<td class="truncate text-secondary">' + escapeHtml(r.description || "—") + '</td><td class="text-end amount ' + (key === "income" ? "text-success" : "") + '">' + formatCurrency(r.amount) + '</td></tr>';
                } else if (key === "investment") {
                    var has = r.current_value !== null && r.current_value !== "";
                    var gl = has ? parseFloat(r.current_value) - parseFloat(r.amount) : null;
                    html += '<tr><td class="cell-title">' + escapeHtml(r.investment_name || "—") + '</td><td><span class="pill pill-neutral">' + escapeHtml(r.investment_type) + '</span></td>';
                    html += '<td class="text-end amount">' + formatCurrency(r.amount) + '</td><td class="text-end amount">' + (has ? formatCurrency(r.current_value) : '<span class="text-muted fw-normal">—</span>') + '</td>';
                    html += '<td class="text-end amount ' + (gl === null ? "" : (gl >= 0 ? "text-success" : "text-danger")) + '">' + (gl === null ? "—" : (gl >= 0 ? "+" : "") + formatCurrency(gl)) + '</td>';
                    html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(r.investment_date)) + '</td></tr>';
                } else {
                    html += '<tr><td class="cell-title">' + escapeHtml(r.account_name) + '</td><td><span class="pill pill-neutral">' + escapeHtml(r.account_type) + '</span></td><td class="text-end amount">' + formatCurrency(r.balance) + '</td></tr>';
                }
            });
            $(t[0]).html(html);
        }, function(msg) {
            loaded[key] = false;
            $(t[0]).html(emptyRow(t[1], "bi-wifi-off", "Couldn't load data", msg));
        });
    }

    function loadAdviceHistory() {
        ajaxPost(URL, { action: "display_advice" }, function(response) {
            var rows = (response.data || []).filter(function(r) { return r.user_id == user_id; });
            if (!rows.length) {
                $("#adviceHistory").html(emptyStateHtml("bi-journal", "No advice sent yet", ""));
                return;
            }
            var html = "";
            rows.forEach(function(r) {
                html += '<div class="list-row align-items-start"><div class="row-main"><div class="fw-semibold">' + escapeHtml(r.title) + '</div>';
                html += '<div class="small text-secondary" style="white-space:pre-wrap;">' + escapeHtml(r.content) + '</div>';
                html += '<div class="cell-sub mt-1">' + escapeHtml(formatDateTime(r.created_at)) + '</div></div></div>';
            });
            $("#adviceHistory").html(html);
        });
    }

    $("#adviceForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");
        var title = $("#adviceTitle").val().trim();
        var content = $("#adviceContent").val().trim();
        if (!title || !content) {
            if (!title) $("#adviceTitle").addClass("is-invalid");
            if (!content) $("#adviceContent").addClass("is-invalid");
            toast("danger", "Please add a title and your advice.");
            return;
        }
        var $form = $(this);
        setFormLoading($form, true);
        ajaxPost(URL, { action: "add_advice", user_id: user_id, title: title, content: content }, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                toast("success", "Advice sent. The client has been notified.");
                $form[0].reset();
                $("#adviceCount").text(0);
                loadAdviceHistory();
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setFormLoading($form, false);
            toast("danger", msg);
        });
    });
});
</script>
