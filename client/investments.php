<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/BankAccount.php';

$active = "investments";

$accounts = (new BankAccount())->getAccounts($_SESSION['user_id']);
$account_options = [];
while ($accounts && ($row = $accounts->fetch_assoc())) {
    $account_options[] = $row;
}
$has_accounts = count($account_options) > 0;

$title = "Investments";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Investments</h1>
        <p>Track what you've invested and how it's performing.</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" id="btnAdd" <?php echo $has_accounts ? '' : 'disabled'; ?>><i class="bi bi-plus-lg"></i> Add investment</button>
    </div>
</div>

<?php if (!$has_accounts) { ?>
    <div class="notice">
        <i class="bi bi-info-circle"></i>
        <div>You need at least one bank account before you can record investments. <a href="bank_accounts.php" class="fw-semibold">Add a bank account →</a></div>
    </div>
<?php } ?>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Total invested</span><span class="icon-chip chip-primary"><i class="bi bi-wallet2"></i></span></div>
            <div class="stat-value" id="totalInvested">—</div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Current value</span><span class="icon-chip chip-info"><i class="bi bi-cash-stack"></i></span></div>
            <div class="stat-value" id="currentValue">—</div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Gain / loss</span><span class="icon-chip chip-neutral" id="gainChip"><i class="bi bi-graph-up"></i></span></div>
            <div class="stat-value" id="totalGainLoss">—</div>
            <div class="stat-meta" id="returnPct"></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Holdings</span><span class="icon-chip chip-neutral"><i class="bi bi-collection"></i></span></div>
            <div class="stat-value" id="totalInvestments">—</div>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-header">
        <h3 class="card-title">Your investments</h3>
        <div class="search-input" style="max-width: 340px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search name, type, account…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Investment</th>
                    <th>Type</th>
                    <th class="text-end">Invested</th>
                    <th class="text-end">Current value</th>
                    <th class="text-end">Gain / loss</th>
                    <th>Account</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="investmentTable"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="invModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" id="invForm" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="invModalTitle">Add investment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="inv_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="inv_name">Name<span class="req">*</span></label>
                        <input type="text" class="form-control" id="inv_name" placeholder="e.g. Jollibee shares, Pag-IBIG MP2" maxlength="100" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="inv_type_select">Type<span class="req">*</span></label>
                        <select class="form-select" id="inv_type_select" required>
                            <option value="">Select type</option>
                            <option value="Stocks">Stocks</option>
                            <option value="Mutual Funds">Mutual Funds</option>
                            <option value="Bonds">Bonds</option>
                            <option value="Crypto">Crypto</option>
                            <option value="Real Estate">Real Estate</option>
                            <option value="Other">Other…</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="inv_type_custom" placeholder="Enter investment type" maxlength="100" style="display:none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="inv_amount">Amount invested<span class="req">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="inv_amount" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="inv_current">Current value</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="inv_current" step="0.01" min="0" placeholder="Leave blank if unknown">
                        </div>
                        <div class="form-text">Update this anytime to track your gain or loss.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="inv_account">Funded from<span class="req">*</span></label>
                        <select class="form-select" id="inv_account" required>
                            <?php if (count($account_options) > 1) { ?><option value="">Select account</option><?php } ?>
                            <?php foreach ($account_options as $acc) { ?>
                                <option value="<?php echo intval($acc['account_id']); ?>"><?php echo e($acc['account_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="inv_date">Date invested<span class="req">*</span></label>
                        <input type="date" class="form-control" id="inv_date" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="inv_description">Notes <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" class="form-control" id="inv_description" maxlength="500" placeholder="Anything worth remembering">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="invSave">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>var HAS_ACCOUNTS = <?php echo $has_accounts ? 'true' : 'false'; ?>;</script>
<script src="<?php echo JS_PATH; ?>/investment.js"></script>
