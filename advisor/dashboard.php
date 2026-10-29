<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advice.php';
require_once '../classes/AdvisorPermission.php';
require_once '../classes/Rating.php';
require_once '../classes/Message.php';

$active = "dashboard";
$advisor_id = $_SESSION['user_id'];

$clients = [];
$res = (new AdvisorPermission())->getPermittedClientsForAdvisor($advisor_id);
while ($res && ($row = $res->fetch_assoc())) {
    $clients[] = $row;
}
usort($clients, function($a, $b) { return strcmp($b['created_at'], $a['created_at']); });

$advice_list = [];
$res = (new Advice())->getAdviceByAdvisor($advisor_id);
while ($res && ($row = $res->fetch_assoc())) {
    $advice_list[] = $row;
}

$rating = new Rating();
$avg_rating = $rating->getAverageRating($advisor_id);
$rating_count = $rating->getRatingCount($advisor_id);
$unread_messages = (new Message())->getUnreadCount($advisor_id);

$first_name = explode(' ', trim($_SESSION['full_name']))[0];

$title = "Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="hero-card">
    <div>
        <h2>Welcome back, <?php echo e($first_name); ?></h2>
        <p>You're advising <?php echo count($clients); ?> client<?php echo count($clients) == 1 ? '' : 's'; ?>. Here's what's happening.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="clients.php" class="btn btn-light"><i class="bi bi-people"></i> My clients</a>
        <a href="messages.php" class="btn btn-outline-light"><i class="bi bi-chat-dots"></i> Messages</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="clients.php">
            <div class="stat-top"><span class="stat-label">Clients</span><span class="icon-chip chip-primary"><i class="bi bi-people"></i></span></div>
            <div class="stat-value"><?php echo count($clients); ?></div>
            <div class="stat-meta">Sharing data with you</div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-label">Advice given</span><span class="icon-chip chip-success"><i class="bi bi-journal-text"></i></span></div>
            <div class="stat-value"><?php echo count($advice_list); ?></div>
            <div class="stat-meta">All time</div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="ratings.php">
            <div class="stat-top"><span class="stat-label">Average rating</span><span class="icon-chip chip-warning"><i class="bi bi-star"></i></span></div>
            <div class="stat-value"><?php echo $rating_count ? number_format($avg_rating, 1) . ' <span class="fs-6 text-muted fw-semibold">/ 5</span>' : '—'; ?></div>
            <div class="stat-meta"><?php echo $rating_count; ?> rating<?php echo $rating_count == 1 ? '' : 's'; ?></div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a class="stat-card" href="messages.php">
            <div class="stat-top"><span class="stat-label">Unread messages</span><span class="icon-chip chip-info"><i class="bi bi-chat-dots"></i></span></div>
            <div class="stat-value"><?php echo intval($unread_messages); ?></div>
            <div class="stat-meta"><?php echo $unread_messages ? 'Clients are waiting for a reply' : "You're all caught up"; ?></div>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Newest clients</h3>
                <a href="clients.php" class="small fw-semibold">View all</a>
            </div>
            <?php if ($clients) { ?>
                <?php foreach (array_slice($clients, 0, 5) as $c) { ?>
                    <a class="list-row" href="client_profile.php?user_id=<?php echo intval($c['client_id']); ?>">
                        <span class="avatar sm"><?php echo e(initials($c['full_name'])); ?></span>
                        <div class="row-main">
                            <div class="row-title"><?php echo e($c['full_name']); ?></div>
                            <div class="row-sub">Connected <?php echo e(formatDate($c['created_at'])); ?></div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-people"></i></div>
                    <h6>No clients yet</h6>
                    <p>Clients appear here once they share their data with you. A complete profile helps them choose you.</p>
                    <a href="profile.php" class="btn btn-outline-primary btn-sm">Complete your profile</a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Recent advice</h3>
            </div>
            <?php if ($advice_list) { ?>
                <?php foreach (array_slice($advice_list, 0, 5) as $a) { ?>
                    <a class="list-row" href="client_profile.php?user_id=<?php echo intval($a['user_id']); ?>">
                        <span class="icon-chip chip-primary"><i class="bi bi-lightbulb"></i></span>
                        <div class="row-main">
                            <div class="row-title"><?php echo e($a['title']); ?></div>
                            <div class="row-sub">To <?php echo e($a['client_name']); ?> · <?php echo e(formatDate($a['created_at'])); ?></div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-journal-text"></i></div>
                    <h6>No advice sent yet</h6>
                    <p>Advice you write for clients will be listed here.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
