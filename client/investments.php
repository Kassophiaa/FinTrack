<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Investment.php';
require_once '../classes/BankAccount.php';

$active = "investments";

$bankAccount = new BankAccount();
$accounts = $bankAccount->getAccounts($_SESSION['user_id']);

$title = "Investments";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">
        <h4 class="mb-4"><i class="bi bi-graph-up-arrow"></i> Investment Management</h4>

        <div id="alert"></div>

        <div class="row mb-4" id="summaryCards">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-primary investment-summary-card">
                    <span class="card-icon"><i class="bi bi-wallet2"></i></span>
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-wallet2"></i> Total Invested</h6>
                        <h5 id="totalInvested">&#8369;0.00</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-info investment-summary-card">
                    <span class="card-icon"><i class="bi bi-cash-stack"></i></span>
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-cash-stack"></i> Current Value</h6>
                        <h5 id="currentValue">&#8369;0.00</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white investment-summary-card" id="gainLossCard">
                    <span class="card-icon"><i class="bi bi-graph-up"></i></span>
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-graph-up"></i> Total Gain/Loss</h6>
                        <h5 id="totalGainLoss">&#8369;0.00</h5>
                        <small><i class="bi bi-percent"></i> Return: <span id="returnPct">0.00%</span></small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-dark investment-summary-card">
                    <span class="card-icon"><i class="bi bi-bar-chart"></i></span>
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-bar-chart"></i> Investments</h6>
                        <h5 id="totalInvestments">0</h5>
                        <small>Active records</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 investment-form-card">
            <div class="card-header"><i class="bi bi-plus-circle"></i> Add New Investment</div>
            <div class="card-body">
                <form id="investmentForm">
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
                            <label class="form-label">Investment Type *</label>
                            <select class="form-select" id="investment_type_select" required>
                                <option value="">-- Select Type --</option>
                                <option value="Stocks">Stocks</option>
                                <option value="Mutual Funds">Mutual Funds</option>
                                <option value="Bonds">Bonds</option>
                                <option value="Crypto">Crypto</option>
                                <option value="Real Estate">Real Estate</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-1" id="investment_type_custom" placeholder="Enter custom type" style="display:none;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Investment Name *</label>
                            <input type="text" class="form-control" id="investment_name" placeholder="e.g. MPI Shares" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Amount Invested *</label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input type="number" class="form-control" id="amount" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Current Value *</label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input type="number" class="form-control" id="current_value" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" id="investment_date" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <input type="text" class="form-control" id="description" placeholder="Optional">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-dark btn-add"><i class="bi bi-plus-circle"></i> Add Investment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card investment-table-card">
            <div class="card-header">
                <span><i class="bi bi-list"></i> Investment Records</span>
            </div>
            <div class="card-body">
                <div class="search-bar input-group mb-0">
                    <input type="text" class="form-control" id="searchKeyword" placeholder="Search by type, name, account, amount, or date...">
                    <button class="btn btn-dark" id="btnSearch"><i class="bi bi-search"></i> Search</button>
                    <button class="btn btn-secondary" id="btnClear"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-hover investment-table">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th class="text-end">Invested</th>
                                <th class="text-end">Current Value</th>
                                <th class="text-end">Gain/Loss</th>
                                <th class="text-center">Return %</th>
                                <th>Account</th>
                                <th>Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="investmentTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade investment-edit-modal" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Investment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_investment_id">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account *</label>
                        <select class="form-select" id="edit_account_id">
                            <?php if ($accounts && $accounts->num_rows > 0) { ?>
                                <?php $accounts->data_seek(0); while ($row = $accounts->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['account_id']; ?>"><?php echo $row['account_name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Investment Type *</label>
                        <select class="form-select" id="edit_investment_type_select">
                            <option value="">-- Select Type --</option>
                            <option value="Stocks">Stocks</option>
                            <option value="Mutual Funds">Mutual Funds</option>
                            <option value="Bonds">Bonds</option>
                            <option value="Crypto">Crypto</option>
                            <option value="Real Estate">Real Estate</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control mt-1" id="edit_investment_type_custom" placeholder="Enter custom type" style="display:none;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Investment Name *</label>
                        <input type="text" class="form-control" id="edit_investment_name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" id="edit_investment_date">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount Invested *</label>
                        <div class="input-group">
                            <span class="input-group-text">&#8369;</span>
                            <input type="number" class="form-control" id="edit_amount" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Current Value</label>
                        <div class="input-group">
                            <span class="input-group-text">&#8369;</span>
                            <input type="number" class="form-control" id="edit_current_value" step="0.01" min="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <input type="text" class="form-control" id="edit_description">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancel</button>
                <button type="button" class="btn btn-dark" id="btnSaveEdit"><i class="bi bi-check-circle"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>

<link href="<?php echo CSS_PATH; ?>/investment.css" rel="stylesheet">
<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/investment.js"></script>
