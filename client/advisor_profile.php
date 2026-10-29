<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advice.php';
require_once '../classes/Rating.php';

$active = "advisors";

$advisor_id = isset($_GET['advisor_id']) ? $_GET['advisor_id'] : 0;

$title = "Advisor Profile";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="mb-3">
            <a href="advisors.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Advisors</a>
        </div>

        <h4 class="mb-4">Advisor Profile</h4>

        <div id="alert"></div>

        <div class="card mb-4" id="profileCard">
            <div class="card-body text-center">
                <i class="bi bi-person-circle" style="font-size: 5rem;"></i>
                <h3 id="advisorName">Loading...</h3>
                <p class="text-muted" id="advisorEmail"></p>
                <p id="advisorRating"></p>
            </div>
        </div>

        <div id="advisorBio" class="mt-3"></div>

        <div class="card mb-4">
            <div class="card-body text-center">
                <button class="btn btn-primary" id="btnSeekAdvice"><i class="bi bi-key"></i> Seek Financial Advice</button>
                <p class="text-muted mt-2 small">Grant this advisor access to view your financial data and provide personalized advice.</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-journal-text"></i> Advice History</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="adviceTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-star"></i> Rate This Advisor</div>
            <div class="card-body">
                <form id="rateForm">
                    <input type="hidden" id="rate_advisor_id" value="<?php echo $advisor_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select class="form-select" id="rate_rating" required>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Below Average</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea class="form-control" id="rate_comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-star"></i> Submit Rating</button>
                </form>
            </div>
        </div>
        <div class="modal fade" id="permissionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Seek Financial Advice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Choose which financial data you want to share with this advisor:</p>
                        <input type="hidden" id="perm_advisor_id" value="<?php echo $advisor_id; ?>">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_view_income">
                            <label class="form-check-label" for="perm_view_income"><i class="bi bi-arrow-down-circle"></i> View Income</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_view_expenses">
                            <label class="form-check-label" for="perm_view_expenses"><i class="bi bi-arrow-up-circle"></i> View Expenses</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_view_investments">
                            <label class="form-check-label" for="perm_view_investments"><i class="bi bi-graph-up"></i> View Investments</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_view_bank_accounts">
                            <label class="form-check-label" for="perm_view_bank_accounts"><i class="bi bi-bank"></i> View Bank Accounts</label>
                        </div>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_can_message" checked>
                            <label class="form-check-label" for="perm_can_message"><i class="bi bi-chat-dots"></i> Allow Messaging</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_can_give_advice" checked>
                            <label class="form-check-label" for="perm_can_give_advice"><i class="bi bi-journal-text"></i> Allow Advice</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="perm_can_rate">
                            <label class="form-check-label" for="perm_can_rate"><i class="bi bi-star"></i> Allow Rating</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btnSavePermission"><i class="bi bi-check-circle"></i> Save Permissions</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/advisor.js"></script>
