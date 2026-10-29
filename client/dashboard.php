<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Income.php';
require_once '../classes/Expense.php';
require_once '../classes/Investment.php';
require_once '../classes/BankAccount.php';
require_once '../classes/Advice.php';
require_once '../classes/Notification.php';

$active = "dashboard";

$income = new Income();
$expense = new Expense();
$investment = new Investment();
$account = new BankAccount();
$advice = new Advice();
$notif = new Notification();

$user_id = $_SESSION['user_id'];

$total_income = $income->getTotalIncome($user_id);
$total_expense = $expense->getTotalExpense($user_id);
$total_investment = $investment->getTotalInvestment($user_id);
$total_balance = $account->getTotalBalance($user_id);
$monthly_income = $income->getMonthlyIncome($user_id);
$monthly_expense = $expense->getMonthlyExpense($user_id);
$unread_notif = $notif->getUnreadCount($user_id);

$recent_advice = $advice->getAdviceByUser($user_id);

$title = "Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="welcome-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5>Welcome back, <?php echo $_SESSION['full_name']; ?>!</h5>
                    <p>Here's an overview of your financial status.</p>
                </div>
                <div class="welcome-icon d-none d-md-block">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-plus-circle"></i></div>
                        <div class="card-title">Total Income</div>
                        <h5><?php echo formatCurrency($total_income); ?></h5>
                        <small><i class="bi bi-calendar"></i> This month: <?php echo formatCurrency($monthly_income); ?></small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-stat-card bg-danger text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-dash-circle"></i></div>
                        <div class="card-title">Total Expenses</div>
                        <h5><?php echo formatCurrency($total_expense); ?></h5>
                        <small><i class="bi bi-calendar"></i> This month: <?php echo formatCurrency($monthly_expense); ?></small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <div class="card-title">Investments</div>
                        <h5><?php echo formatCurrency($total_investment); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="dashboard-stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="card-icon"><i class="bi bi-bank"></i></div>
                        <div class="card-title">Bank Balance</div>
                        <h5><?php echo formatCurrency($total_balance); ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dashboard-summary-card">
                    <div class="card-body">
                        <div class="summary-icon bg-success-subtle">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>
                        <div>
                            <h6>Monthly Income</h6>
                            <h5><?php echo formatCurrency($monthly_income); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-summary-card">
                    <div class="card-body">
                        <div class="summary-icon bg-danger-subtle">
                            <i class="bi bi-arrow-up-circle"></i>
                        </div>
                        <div>
                            <h6>Monthly Expenses</h6>
                            <h5><?php echo formatCurrency($monthly_expense); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-summary-card">
                    <div class="card-body">
                        <div class="summary-icon bg-info-subtle">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div>
                            <h6>Unread Notifications</h6>
                            <h5><?php echo $unread_notif; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-advice-card">
            <div class="card-header">
                <i class="bi bi-journal-text"></i> Recent Advice from Advisor
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_advice && $recent_advice->num_rows > 0) { ?>
                                <?php $count = 0; while ($row = $recent_advice->fetch_assoc()) { ?>
                                <tr>
                                    <td><strong><?php echo $row['title']; ?></strong></td>
                                    <td style="color: var(--text-secondary);"><?php echo substr($row['content'], 0, 60); ?>...</td>
                                    <td style="color: var(--text-muted); font-size: 0.85rem;"><?php echo $row['created_at']; ?></td>
                                </tr>
                                <?php $count++; if ($count >= 5) break; } ?>
                            <?php } else { ?>
                                <tr><td colspan="3" class="text-center">No advice yet</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
