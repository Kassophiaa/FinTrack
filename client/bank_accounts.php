<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/BankAccount.php';

$active = "bank_accounts";

$title = "Bank Accounts";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-bank" style="color: var(--primary);"></i> Bank Accounts</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-plus-circle"></i> Add Account</div>
            <div class="card-body">
                <form id="accountForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Account Name *</label>
                            <input type="text" class="form-control" id="account_name" placeholder="e.g. BDO Savings" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Account Type *</label>
                            <select class="form-select" id="account_type_select" required>
                                <option value="">-- Select Type --</option>
                                <option value="Savings">Savings</option>
                                <option value="Checking">Checking</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Cash">Cash</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-1" id="account_type_custom" placeholder="Enter custom account type" style="display:none;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Balance *</label>
                            <input type="number" class="form-control" id="balance" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Add Account</button>
                </form>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header"><i class="bi bi-list-ul"></i> Account List</div>
            <div class="card-body">
                <div class="search-bar mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Search accounts...">
                        <button class="btn btn-dark" id="btnSearch"><i class="bi bi-search"></i></button>
                        <button class="btn btn-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Account Name</th>
                                <th>Type</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="accountTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_account_id">
                <div class="mb-3">
                    <label class="form-label">Account Name *</label>
                    <input type="text" class="form-control" id="edit_account_name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Account Type *</label>
                    <select class="form-select" id="edit_account_type_select">
                        <option value="">-- Select Type --</option>
                        <option value="Savings">Savings</option>
                        <option value="Checking">Checking</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Cash">Cash</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control mt-1" id="edit_account_type_custom" placeholder="Enter custom account type" style="display:none;">
                </div>
                <div class="mb-3">
                    <label class="form-label">Balance *</label>
                    <input type="number" class="form-control" id="edit_balance" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dark" id="btnSaveEdit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/bank.js"></script>
