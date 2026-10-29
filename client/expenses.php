<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Expense.php';
require_once '../classes/BankAccount.php';

$active = "expenses";

$bankAccount = new BankAccount();
$accounts = $bankAccount->getAccounts($_SESSION['user_id']);

$title = "Expenses";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-dash-circle" style="color: var(--danger);"></i> Expense Management</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-plus-circle"></i> Add Expense</div>
            <div class="card-body">
                <form id="expenseForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Account *</label>
                            <select class="form-select" id="account_id" required>
                                <option value="">-- Select Account --</option>
                                <?php if ($accounts && $accounts->num_rows > 0) { ?>
                                    <?php while ($row = $accounts->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['account_id']; ?>"><?php echo $row['account_name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Expense Category *</label>
                            <select class="form-select" id="expense_type_select" required>
                                <option value="">-- Select Category --</option>
                                <option value="Food">Food</option>
                                <option value="Transportation">Transportation</option>
                                <option value="Housing">Housing</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Education">Education</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Shopping">Shopping</option>
                                <option value="Bills">Bills</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-1" id="expense_type_custom" placeholder="Enter custom category" style="display:none;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount *</label>
                            <input type="number" class="form-control" id="amount" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <input type="text" class="form-control" id="description" placeholder="Brief description">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" id="expense_date" required>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Add Expense</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header"><i class="bi bi-list-ul"></i> Expense Records</div>
            <div class="card-body">
                <div class="search-bar mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Search by category, description, amount, or date...">
                        <button class="btn btn-dark" id="btnSearch"><i class="bi bi-search"></i></button>
                        <button class="btn btn-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Account</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="expenseTable">
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
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_expense_id">
                <div class="mb-3">
                    <label class="form-label">Account *</label>
                    <select class="form-select" id="edit_account_id">
                        <?php if ($accounts && $accounts->num_rows > 0) { ?>
                            <?php $accounts->data_seek(0); while ($row = $accounts->fetch_assoc()) { ?>
                                <option value="<?php echo $row['account_id']; ?>"><?php echo $row['account_name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expense Category *</label>
                    <select class="form-select" id="edit_expense_type_select">
                        <option value="">-- Select Category --</option>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Housing">Housing</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Education">Education</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Bills">Bills</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control mt-1" id="edit_expense_type_custom" placeholder="Enter custom category" style="display:none;">
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" class="form-control" id="edit_amount" step="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description (Optional)</label>
                    <input type="text" class="form-control" id="edit_description">
                </div>
                <div class="mb-3">
                    <label class="form-label">Date *</label>
                    <input type="date" class="form-control" id="edit_expense_date">
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

<script src="<?php echo JS_PATH; ?>/expense.js"></script>
