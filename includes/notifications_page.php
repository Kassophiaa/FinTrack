<div class="page-header">
    <div>
        <h1>Notifications</h1>
        <p>Updates about your advisors, clients, advice and ratings.</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-light" id="btnMarkAll"><i class="bi bi-check2-all"></i> Mark all as read</button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs border-0" id="notifFilter">
            <li class="nav-item"><button class="nav-link active" data-filter="all" type="button">All</button></li>
            <li class="nav-item"><button class="nav-link" data-filter="unread" type="button">Unread <span class="pill pill-danger ms-1" id="unreadCount" style="display:none;"></span></button></li>
        </ul>
        <div class="search-input" style="max-width: 300px;">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchKeyword" placeholder="Search notifications…">
            <button type="button" class="btn-clear-search" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
        </div>
    </div>
    <div id="notifList"></div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/notification.js"></script>
