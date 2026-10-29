<?php
// Shared page for income and expenses. Expects $tx (config array) and $accounts (mysqli result).

$account_options = [];
if ($accounts) {
    while ($row = $accounts->fetch_assoc()) {
        $account_options[] = $row;
    }
}
$has_accounts = count($account_options) > 0;

?>

<div class="page-header">
    <div>
        <h1><?php echo e($tx['plural_title']); ?></h1>
        <p><?php echo e($tx['intro']); ?></p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" id="btnAdd" <?php echo $has_accounts ? '' : 'disabled'; ?>><i class="bi bi-plus-lg"></i> Add <?php echo e($tx['noun']); ?></button>
    </div>
</div>

<?php if (!$has_accounts) { ?>
    <div class="notice">
        <i class="bi bi-info-circle"></i>
        <div>You need at least one bank account before you can record <?php echo e($tx['noun']); ?>. <a href="bank_accounts.php" class="fw-semibold">Add a bank account →</a></div>
    </div>
<?php } ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">This month</span><span class="icon-chip <?php echo $tx['chip']; ?>"><i class="bi <?php echo $tx['icon']; ?>"></i></span></div>
            <div class="stat-value" id="statMonth">—</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">All time</span><span class="icon-chip chip-neutral"><i class="bi bi-calendar3"></i></span></div>
            <div class="stat-value" id="statTotal">—</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Records</span><span class="icon-chip chip-neutral"><i class="bi bi-list-ul"></i></span></div>
            <div class="stat-value" id="statCount">—</div>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-header">
        <h3 class="card-title"><?php echo e($tx['plural_title']); ?> history</h3>
        <div class="search-input" style="max-width: 340px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search <?php echo e(strtolower($tx['type_label'])); ?>, description, amount…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th><?php echo e($tx['type_label']); ?></th>
                    <th>Description</th>
                    <th>Account</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="txTable"></tbody>
        </table>
    </div>
    <div class="table-footer-summary" id="txSummary" style="display: none;"></div>
</div>

<div class="modal fade" id="txModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="txForm" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="txModalTitle">Add <?php echo e($tx['noun']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tx_id">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label" for="tx_amount">Amount<span class="req">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="tx_amount" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="tx_date">Date<span class="req">*</span></label>
                        <input type="date" class="form-control" id="tx_date" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="tx_type_select"><?php echo e($tx['type_label']); ?><span class="req">*</span></label>
                        <select class="form-select" id="tx_type_select" required>
                            <option value="">Select <?php echo e(strtolower($tx['type_label'])); ?></option>
                            <?php foreach ($tx['types'] as $t) { ?>
                                <option value="<?php echo e($t); ?>"><?php echo e($t); ?></option>
                            <?php } ?>
                            <option value="Other">Other…</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="tx_type_custom" placeholder="Enter <?php echo e(strtolower($tx['type_label'])); ?>" maxlength="100" style="display:none;">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="tx_account"><?php echo e($tx['account_label']); ?><span class="req">*</span></label>
                        <select class="form-select" id="tx_account" required>
                            <?php if (count($account_options) > 1) { ?><option value="">Select account</option><?php } ?>
                            <?php foreach ($account_options as $acc) { ?>
                                <option value="<?php echo intval($acc['account_id']); ?>"><?php echo e($acc['account_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="tx_description">Description <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" class="form-control" id="tx_description" maxlength="500" placeholder="<?php echo e($tx['desc_placeholder']); ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="txSave">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script>
var TX = <?php echo json_encode([
    'module' => $tx['module'],
    'idField' => $tx['id_field'],
    'typeField' => $tx['type_field'],
    'dateField' => $tx['date_field'],
    'noun' => $tx['noun'],
    'sign' => $tx['sign'],
    'hasAccounts' => $has_accounts,
]); ?>;
</script>
<script src="<?php echo asset("js/transactions.js"); ?>"></script>
