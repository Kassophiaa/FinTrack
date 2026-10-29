<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "bank_accounts";

$title = "Accounts";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="page-header">
    <div>
        <h1>Bank accounts &amp; wallets</h1>
        <p>Add the places where you keep your money. Income and expenses are recorded against these.</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" id="btnAdd"><i class="bi bi-plus-lg"></i> Add account</button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Total balance</span><span class="icon-chip chip-primary"><i class="bi bi-wallet2"></i></span></div>
            <div class="stat-value" id="statTotal">—</div>
            <div class="stat-meta" id="statCount"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-8 d-flex align-items-center">
        <div class="search-input w-100" style="max-width: 360px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search by name or type…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
</div>

<div class="row g-3" id="accountList"></div>

<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="accountForm" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="accountModalTitle">Add account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="acc_id">
                <div class="mb-3">
                    <label class="form-label" for="acc_name">Account name<span class="req">*</span></label>
                    <input type="text" class="form-control" id="acc_name" placeholder="e.g. BDO Savings, GCash" maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="acc_type_select">Type<span class="req">*</span></label>
                    <select class="form-select" id="acc_type_select" required>
                        <option value="">Select type</option>
                        <option value="Savings">Savings</option>
                        <option value="Checking">Checking</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Cash">Cash</option>
                        <option value="Other">Other…</option>
                    </select>
                    <input type="text" class="form-control mt-2" id="acc_type_custom" placeholder="Enter account type" maxlength="100" style="display:none;">
                </div>
                <div>
                    <label class="form-label" for="acc_balance">Current balance<span class="req">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="acc_balance" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="accSave">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/bank.js"></script>
