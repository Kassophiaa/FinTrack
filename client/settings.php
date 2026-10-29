<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Income.php';
require_once '../classes/Expense.php';
require_once '../classes/Investment.php';
require_once '../classes/BankAccount.php';

$active = "settings";

$user_id = $_SESSION['user_id'];

$income = new Income();
$expense = new Expense();
$investment = new Investment();
$account = new BankAccount();

$total_income = $income->getTotalIncome($user_id);
$total_expense = $expense->getTotalExpense($user_id);
$total_investment = $investment->getTotalInvestment($user_id);
$total_balance = $account->getTotalBalance($user_id);

$title = "Settings";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-gear" style="color: var(--primary);"></i> Settings</h4>
        </div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-person-circle"></i> Account Summary</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin-bottom: 8px;"><strong>Full Name:</strong> <span style="color: var(--text-secondary);"><?php echo $_SESSION['full_name']; ?></span></p>
                        <p style="margin-bottom: 8px;"><strong>Username:</strong> <span style="color: var(--text-secondary);"><?php echo $_SESSION['username']; ?></span></p>
                        <p class="mb-0"><strong>Role:</strong> <span class="badge" style="background: var(--primary); color: #fff;"><?php echo ucfirst($_SESSION['role']); ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="quick-stat">
                                    <div class="stat-icon" style="background: rgba(22,163,74,0.1); color: var(--success);"><i class="bi bi-plus-circle"></i></div>
                                    <div>
                                        <h6>Income</h6>
                                        <h5 style="color: var(--success);"><?php echo formatCurrency($total_income); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="quick-stat">
                                    <div class="stat-icon" style="background: rgba(220,38,38,0.1); color: var(--danger);"><i class="bi bi-dash-circle"></i></div>
                                    <div>
                                        <h6>Expenses</h6>
                                        <h5 style="color: var(--danger);"><?php echo formatCurrency($total_expense); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="quick-stat">
                                    <div class="stat-icon" style="background: rgba(8,145,178,0.1); color: var(--info);"><i class="bi bi-graph-up"></i></div>
                                    <div>
                                        <h6>Investments</h6>
                                        <h5 style="color: var(--info);"><?php echo formatCurrency($total_investment); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="quick-stat">
                                    <div class="stat-icon" style="background: rgba(26,86,50,0.1); color: var(--primary);"><i class="bi bi-bank"></i></div>
                                    <div>
                                        <h6>Balance</h6>
                                        <h5 style="color: var(--primary);"><?php echo formatCurrency($total_balance); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-lightning"></i> Quick Links</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <a href="profile.php" class="btn btn-outline-dark w-100" style="border-radius: var(--radius-sm); padding: 10px;">
                            <i class="bi bi-person"></i> Edit Profile
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="privacy_settings.php" class="btn btn-outline-dark w-100" style="border-radius: var(--radius-sm); padding: 10px;">
                            <i class="bi bi-shield-lock"></i> Change Password
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="bank_accounts.php" class="btn btn-outline-dark w-100" style="border-radius: var(--radius-sm); padding: 10px;">
                            <i class="bi bi-bank"></i> Manage Accounts
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="card-header"><i class="bi bi-box-arrow-right"></i> Session</div>
            <div class="card-body">
                <p class="mb-3" style="color: var(--text-secondary);">You are logged in as <strong><?php echo $_SESSION['full_name']; ?></strong> (<?php echo ucfirst($_SESSION['role']); ?>).</p>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
