        </main>
    </div>
</div>

<div class="toast-stack" id="toastStack" aria-live="polite"></div>

<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center pt-4">
                <div class="confirm-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <h5 class="mb-2" id="confirmTitle">Are you sure?</h5>
                <p class="text-secondary mb-0" id="confirmMessage"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmOk">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var BASE_URL = "<?php echo BASE_URL; ?>";
var MY_ID = "<?php echo intval($_SESSION['user_id']); ?>";
</script>
<script src="<?php echo JS_PATH; ?>/script.js"></script>
