<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Income.php';
require_once '../classes/Expense.php';
require_once '../classes/Investment.php';
require_once '../classes/BankAccount.php';
require_once '../classes/Advice.php';
require_once '../classes/AdvisorPermission.php';

$active = "dashboard";
$user_id = $_SESSION['user_id'];

$income = new Income();
$expense = new Expense();
$investment = new Investment();
$account = new BankAccount();
$advice = new Advice();

$total_balance = $account->getTotalBalance($user_id);
$monthly_income = $income->getMonthlyIncome($user_id);
$monthly_expense = $expense->getMonthlyExpense($user_id);
$net_month = $monthly_income - $monthly_expense;
$savings_rate = $monthly_income > 0 ? round(($net_month / $monthly_income) * 100) : null;
$inv_summary = $investment->getInvestmentSummary($user_id);

$accounts_result = $account->getAccounts($user_id);
$account_count = $accounts_result ? $accounts_result->num_rows : 0;

// Chart data: last 6 months
$income_by_month = $income->getMonthlyTotals($user_id, 6);
$expense_by_month = $expense->getMonthlyTotals($user_id, 6);
$chart_labels = $chart_income = $chart_expense = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime(date('Y-m-01') . " -$i months");
    $key = date('Y-m', $ts);
    $chart_labels[] = date('M Y', $ts);
    $chart_income[] = round($income_by_month[$key] ?? 0, 2);
    $chart_expense[] = round($expense_by_month[$key] ?? 0, 2);
}
$has_chart_data = array_sum($chart_income) + array_sum($chart_expense) > 0;

$categories = array_slice($expense->getCategoryTotalsThisMonth($user_id), 0, 5);
$max_category = $categories ? $categories[0]['total'] : 0;

// Recent transactions: newest 6 income + expense records combined
$recent = [];
$res = $income->getIncome($user_id);
while ($res && ($row = $res->fetch_assoc()) && count($recent) < 6) {
    $recent[] = ['kind' => 'income', 'type' => $row['income_type'], 'desc' => $row['description'], 'amount' => $row['amount'], 'date' => $row['income_date'], 'account' => $row['account_name']];
}
$res = $expense->getExpense($user_id);
$n = 0;
while ($res && ($row = $res->fetch_assoc()) && $n++ < 6) {
    $recent[] = ['kind' => 'expense', 'type' => $row['expense_type'], 'desc' => $row['description'], 'amount' => $row['amount'], 'date' => $row['expense_date'], 'account' => $row['account_name']];
}
usort($recent, function($a, $b) { return strcmp($b['date'], $a['date']); });
$recent = array_slice($recent, 0, 6);

$recent_advice = [];
$res = $advice->getAdviceByUser($user_id);
while ($res && ($row = $res->fetch_assoc()) && count($recent_advice) < 3) {
    $recent_advice[] = $row;
}

$has_income = $income->getTotalIncome($user_id) > 0;

$advisor_count = 0;
$res = (new AdvisorPermission())->getPermittedAdvisorsForClient($user_id);
if ($res) $advisor_count = $res->num_rows;

$first_name = explode(' ', trim($_SESSION['full_name']))[0];
$hour = intval(date('G'));
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

$title = "Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="hero-card">
    <div>
        <h2><?php echo e($greeting . ', ' . $first_name); ?> 👋</h2>
        <p>Here's how your finances look for <?php echo date('F Y'); ?>.</p>
        <div class="d-flex gap-2 mt-3 flex-wrap">
            <a href="income.php" class="btn btn-light btn-sm"><i class="bi bi-plus-lg"></i> Add income</a>
            <a href="expenses.php" class="btn btn-light btn-sm"><i class="bi bi-plus-lg"></i> Add expense</a>
        </div>
    </div>
    <div class="text-md-end">
        <div class="hero-label">Total balance</div>
        <div class="hero-figure"><?php echo formatCurrency($total_balance); ?></div>
        <div class="small" style="color: rgba(255,255,255,.75);">across <?php echo $account_count; ?> account<?php echo $account_count == 1 ? '' : 's'; ?></div>
    </div>
</div>

<?php if ($account_count == 0 || $advisor_count == 0) { ?>
<div class="card mb-4">
    <div class="card-header"><h3 class="card-title"><i class="bi bi-rocket-takeoff"></i> Get started</h3></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <a class="list-row border rounded-3" href="bank_accounts.php">
                    <span class="icon-chip <?php echo $account_count ? 'chip-success' : 'chip-primary'; ?>"><i class="bi <?php echo $account_count ? 'bi-check-lg' : 'bi-bank'; ?>"></i></span>
                    <div class="row-main"><div class="row-title">Add a bank account</div><div class="row-sub">Needed before recording transactions</div></div>
                </a>
            </div>
            <div class="col-md-4">
                <a class="list-row border rounded-3" href="income.php">
                    <span class="icon-chip <?php echo $has_income ? 'chip-success' : 'chip-primary'; ?>"><i class="bi <?php echo $has_income ? 'bi-check-lg' : 'bi-arrow-down-left'; ?>"></i></span>
                    <div class="row-main"><div class="row-title">Record your income</div><div class="row-sub">Salary, allowance, side jobs…</div></div>
                </a>
            </div>
            <div class="col-md-4">
                <a class="list-row border rounded-3" href="advisors.php">
                    <span class="icon-chip <?php echo $advisor_count ? 'chip-success' : 'chip-primary'; ?>"><i class="bi <?php echo $advisor_count ? 'bi-check-lg' : 'bi-people'; ?>"></i></span>
                    <div class="row-main"><div class="row-title">Connect with an advisor</div><div class="row-sub">Get personalized advice</div></div>
                </a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="income.php">
            <div class="stat-top"><span class="stat-label">Income this month</span><span class="icon-chip chip-success"><i class="bi bi-arrow-down-left"></i></span></div>
            <div class="stat-value"><?php echo formatCurrency($monthly_income); ?></div>
            <div class="stat-meta">View all income →</div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="expenses.php">
            <div class="stat-top"><span class="stat-label">Expenses this month</span><span class="icon-chip chip-danger"><i class="bi bi-arrow-up-right"></i></span></div>
            <div class="stat-value"><?php echo formatCurrency($monthly_expense); ?></div>
            <div class="stat-meta">View all expenses →</div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Net this month</span><span class="icon-chip <?php echo $net_month >= 0 ? 'chip-primary' : 'chip-warning'; ?>"><i class="bi bi-piggy-bank"></i></span></div>
            <div class="stat-value <?php echo $net_month < 0 ? 'text-danger' : ''; ?>"><?php echo formatCurrency($net_month); ?></div>
            <div class="stat-meta"><?php echo $savings_rate === null ? 'No income recorded yet' : ($savings_rate >= 0 ? "You're saving {$savings_rate}% of income" : 'Spending more than you earn'); ?></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="investments.php">
            <div class="stat-top"><span class="stat-label">Investments value</span><span class="icon-chip chip-info"><i class="bi bi-graph-up-arrow"></i></span></div>
            <div class="stat-value"><?php echo formatCurrency($inv_summary['total_current_value']); ?></div>
            <div class="stat-meta">
                <?php if ($inv_summary['total_count'] > 0) { ?>
                    <span class="<?php echo $inv_summary['total_gain_loss'] >= 0 ? 'text-success' : 'text-danger'; ?> fw-semibold"><?php echo ($inv_summary['return_pct'] >= 0 ? '+' : '') . number_format($inv_summary['return_pct'], 2); ?>%</span> overall return
                <?php } else { ?>No investments yet<?php } ?>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Income vs expenses</h3>
                    <p class="card-subtitle">Last 6 months</p>
                </div>
                <div class="chart-legend">
                    <span><i class="swatch" style="background: var(--series-income);"></i>Income</span>
                    <span><i class="swatch" style="background: var(--series-expense);"></i>Expenses</span>
                </div>
            </div>
            <div class="card-body">
                <?php if ($has_chart_data) { ?>
                    <div class="chart-wrap"><canvas id="cashflowChart" aria-label="Bar chart of monthly income and expenses for the last 6 months" role="img"></canvas></div>
                    <table class="visually-hidden">
                        <caption>Monthly income and expenses</caption>
                        <thead><tr><th>Month</th><th>Income</th><th>Expenses</th></tr></thead>
                        <tbody>
                        <?php foreach ($chart_labels as $i => $label) { ?>
                            <tr><td><?php echo e($label); ?></td><td><?php echo formatCurrency($chart_income[$i]); ?></td><td><?php echo formatCurrency($chart_expense[$i]); ?></td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="empty-state"><div class="empty-icon"><i class="bi bi-bar-chart"></i></div><h6>No data yet</h6><p>Add income and expenses to see your monthly trend.</p></div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Top spending</h3>
                    <p class="card-subtitle"><?php echo date('F Y'); ?></p>
                </div>
            </div>
            <div class="card-body">
                <?php if ($categories) { ?>
                    <?php foreach ($categories as $cat) { ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="fw-semibold"><?php echo e($cat['type']); ?></span>
                                <span class="text-secondary"><?php echo formatCurrency($cat['total']); ?></span>
                            </div>
                            <div class="bar-track"><div class="bar-fill" style="width: <?php echo $max_category > 0 ? max(3, round($cat['total'] / $max_category * 100)) : 0; ?>%;"></div></div>
                        </div>
                    <?php } ?>
                    <a href="expenses.php" class="small fw-semibold">See all expenses →</a>
                <?php } else { ?>
                    <div class="empty-state py-4"><div class="empty-icon"><i class="bi bi-cart"></i></div><h6>No spending this month</h6><p>Expenses you add will be grouped by category here.</p></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Recent transactions</h3>
            </div>
            <?php if ($recent) { ?>
                <div>
                    <?php foreach ($recent as $t) { $is_in = $t['kind'] == 'income'; ?>
                        <div class="list-row">
                            <span class="icon-chip <?php echo $is_in ? 'chip-success' : 'chip-danger'; ?>"><i class="bi <?php echo $is_in ? 'bi-arrow-down-left' : 'bi-arrow-up-right'; ?>"></i></span>
                            <div class="row-main">
                                <div class="row-title"><?php echo e($t['type']); ?></div>
                                <div class="row-sub"><?php echo e(formatDate($t['date'])); ?><?php echo $t['account'] ? ' · ' . e($t['account']) : ''; ?><?php echo $t['desc'] ? ' · ' . e($t['desc']) : ''; ?></div>
                            </div>
                            <div class="fw-bold <?php echo $is_in ? 'text-success' : ''; ?>" style="white-space: nowrap;"><?php echo ($is_in ? '+' : '-') . formatCurrency($t['amount']); ?></div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="empty-state"><div class="empty-icon"><i class="bi bi-receipt"></i></div><h6>No transactions yet</h6><p>Your latest income and expenses will appear here.</p></div>
            <?php } ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Latest advice</h3>
                <a href="advice.php" class="small fw-semibold">View all</a>
            </div>
            <?php if ($recent_advice) { ?>
                <div>
                    <?php foreach ($recent_advice as $a) { ?>
                        <a class="list-row" href="advice.php">
                            <span class="avatar sm"><?php echo e(initials($a['advisor_name'])); ?></span>
                            <div class="row-main">
                                <div class="row-title"><?php echo e($a['title']); ?></div>
                                <div class="row-sub"><?php echo e($a['advisor_name']); ?> · <?php echo e(formatDate($a['created_at'])); ?></div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-journal-text"></i></div>
                    <h6>No advice yet</h6>
                    <p>Share your data with an advisor to receive personalized advice.</p>
                    <a href="advisors.php" class="btn btn-outline-primary btn-sm">Find an advisor</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php if ($has_chart_data) { ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
$(function() {
    var css = getComputedStyle(document.documentElement);
    var incomeColor = css.getPropertyValue('--series-income').trim();
    var expenseColor = css.getPropertyValue('--series-expense').trim();

    new Chart(document.getElementById('cashflowChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [
                { label: 'Income', data: <?php echo json_encode($chart_income); ?>, backgroundColor: incomeColor, borderRadius: 4, borderSkipped: 'bottom', maxBarThickness: 26 },
                { label: 'Expenses', data: <?php echo json_encode($chart_expense); ?>, backgroundColor: expenseColor, borderRadius: 4, borderSkipped: 'bottom', maxBarThickness: 26 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            datasets: { bar: { categoryPercentage: 0.6, barPercentage: 0.92 } },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#17201b',
                    padding: 10,
                    boxPadding: 4,
                    callbacks: { label: function(ctx) { return ' ' + ctx.dataset.label + ': ' + formatCurrency(ctx.parsed.y); } }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5a6560', font: { family: 'Inter' } } },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#edf1ee' },
                    ticks: {
                        color: '#8a948f',
                        font: { family: 'Inter' },
                        callback: function(v) { return '₱' + (v >= 1000 ? (v / 1000).toLocaleString() + 'k' : v); }
                    }
                }
            }
        }
    });
});
</script>
<?php } ?>
