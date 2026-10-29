<?php

include '../includes/config.php';
include '../includes/session.php';

$active = "advisors";

$advisor_id = intval($_GET['advisor_id'] ?? 0);
if ($advisor_id <= 0) {
    redirect(CLIENT_PATH . "/advisors.php");
}

$title = "Advisor Profile";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<a href="advisors.php" class="back-link"><i class="bi bi-arrow-left"></i> All advisors</a>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <span class="avatar xl mb-3" id="advisorAvatar">…</span>
                <h2 class="h5 mb-1" id="advisorName">Loading…</h2>
                <div class="text-muted small mb-2" id="advisorEmail"></div>
                <div id="advisorRating"></div>
            </div>
            <div class="card-body border-top" id="advisorAbout">
                <dl class="detail-list">
                    <div><dt>About</dt><dd id="advisorBio" class="text-secondary">—</dd></div>
                    <div><dt>Experience</dt><dd id="advisorExperience" class="text-secondary">—</dd></div>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3" id="accessCard">
            <div class="card-body">
                <div id="accessNone" style="display:none;">
                    <div class="d-flex gap-3 align-items-start flex-wrap">
                        <span class="icon-chip lg chip-primary"><i class="bi bi-person-plus"></i></span>
                        <div class="flex-grow-1">
                            <h3 class="h6 mb-1">Get advice from this advisor</h3>
                            <p class="text-secondary mb-3 small">Choose which parts of your finances to share. You stay in control and can change or stop sharing at any time.</p>
                            <button class="btn btn-primary" id="btnSeekAdvice"><i class="bi bi-shield-check"></i> Share access &amp; request advice</button>
                        </div>
                    </div>
                </div>
                <div id="accessGranted" style="display:none;">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <span class="pill pill-success mb-2"><i class="bi bi-check-circle-fill"></i> Connected</span>
                            <h3 class="h6 mb-1">You're sharing with this advisor</h3>
                            <p class="text-secondary small mb-0">They can see and do the following:</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-primary btn-sm" id="btnMessage" href="messages.php?with=<?php echo $advisor_id; ?>"><i class="bi bi-chat-dots"></i> Message</a>
                            <button class="btn btn-light btn-sm" id="btnEditAccess"><i class="bi bi-sliders"></i> Edit access</button>
                            <button class="btn btn-outline-danger btn-sm" id="btnRevoke"><i class="bi bi-x-circle"></i> Stop sharing</button>
                        </div>
                    </div>
                    <div class="perm-tags" id="permTags"></div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-journal-text"></i> Advice from this advisor</h3>
            </div>
            <div id="adviceList"></div>
        </div>

        <div class="card" id="rateCard" style="display:none;">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-star"></i> Rate this advisor</h3></div>
            <div class="card-body">
                <form id="rateForm" novalidate>
                    <input type="hidden" id="rate_advisor_id" value="<?php echo $advisor_id; ?>">
                    <div class="mb-3">
                        <label class="form-label d-block">Your rating<span class="req">*</span></label>
                        <div class="star-picker" role="radiogroup" aria-label="Rating">
                            <?php for ($s = 5; $s >= 1; $s--) { ?>
                                <input type="radio" name="rate_rating" id="star<?php echo $s; ?>" value="<?php echo $s; ?>">
                                <label for="star<?php echo $s; ?>" title="<?php echo $s; ?> star<?php echo $s > 1 ? 's' : ''; ?>"><i class="bi bi-star-fill"></i></label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="rate_comment">Comment <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea class="form-control" id="rate_comment" rows="3" maxlength="500" placeholder="What was helpful? What could be better?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Submit rating</button>
                </form>
            </div>
        </div>
        <div class="notice info" id="rateLocked" style="display:none;">
            <i class="bi bi-star"></i>
            <div>Want to leave a rating? Turn on <strong>Allow rating</strong> in this advisor's access settings.</div>
        </div>
    </div>
</div>

<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose what to share</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="perm_advisor_id" value="<?php echo $advisor_id; ?>">
                <div class="small-label mb-2">Financial data the advisor can view</div>
                <?php
                $perm_options = [
                    ['perm_view_income', 'bi-arrow-down-left-circle', 'Income', 'Your income records and totals'],
                    ['perm_view_expenses', 'bi-arrow-up-right-circle', 'Expenses', 'Your spending records and totals'],
                    ['perm_view_investments', 'bi-graph-up-arrow', 'Investments', 'Your holdings and performance'],
                    ['perm_view_bank_accounts', 'bi-bank', 'Bank accounts', 'Account names and balances'],
                ];
                foreach ($perm_options as $p) { ?>
                    <label class="perm-option" for="<?php echo $p[0]; ?>">
                        <span class="icon-chip chip-primary"><i class="bi <?php echo $p[1]; ?>"></i></span>
                        <span class="perm-text"><span class="perm-title d-block"><?php echo $p[2]; ?></span><span class="perm-desc"><?php echo $p[3]; ?></span></span>
                        <span class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" id="<?php echo $p[0]; ?>"></span>
                    </label>
                <?php } ?>
                <div class="small-label mt-3 mb-2">Interaction</div>
                <?php
                $perm_options = [
                    ['perm_can_message', 'bi-chat-dots', 'Allow messaging', 'You and the advisor can message each other', true],
                    ['perm_can_give_advice', 'bi-journal-text', 'Allow advice', 'The advisor can send you written advice', true],
                    ['perm_can_rate', 'bi-star', 'Allow rating', 'Lets you leave a rating for this advisor', true],
                ];
                foreach ($perm_options as $p) { ?>
                    <label class="perm-option" for="<?php echo $p[0]; ?>">
                        <span class="icon-chip chip-neutral"><i class="bi <?php echo $p[1]; ?>"></i></span>
                        <span class="perm-text"><span class="perm-title d-block"><?php echo $p[2]; ?></span><span class="perm-desc"><?php echo $p[3]; ?></span></span>
                        <span class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" id="<?php echo $p[0]; ?>" <?php echo $p[4] ? 'checked' : ''; ?>></span>
                    </label>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSavePermission"><i class="bi bi-check-lg"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>var ADVISOR_ID = <?php echo $advisor_id; ?>;</script>
<script src="<?php echo asset("js/advisor.js"); ?>"></script>
