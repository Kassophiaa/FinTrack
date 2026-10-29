<nav class="navbar navbar-expand-lg" style="background: var(--bg-card); border-bottom: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 10px 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>" style="color: var(--primary); font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-wallet2" style="font-size: 1.3rem;"></i>
            FinTrack
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border: none; padding: 4px 8px;">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <span class="nav-link" style="color: var(--text-secondary); font-size: 0.88rem;">
                        <i class="bi bi-person-circle" style="margin-right: 4px;"></i>
                        <?php echo $_SESSION['full_name']; ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-sm" href="<?php echo BASE_URL; ?>/logout.php" style="background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 6px 14px; font-size: 0.85rem; font-weight: 500; transition: all 0.2s;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
