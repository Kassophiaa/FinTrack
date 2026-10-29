<?php
// Shared two-pane messaging page. Expects $contacts: [ ['id','name','sub','can_message'] ], $contact_label, $empty_link.

$message_model = new Message();
$unread_by_sender = $message_model->getUnreadCountsBySender($_SESSION['user_id']);
$selected = intval($_GET['with'] ?? 0);

// Keep past conversations readable even after access was removed
$known_ids = array_map(function($c) { return intval($c['id']); }, $contacts);
foreach ($message_model->getConversationPartners($_SESSION['user_id']) as $partner) {
    if (!in_array(intval($partner['user_id']), $known_ids)) {
        $contacts[] = ['id' => $partner['user_id'], 'name' => $partner['full_name'], 'sub' => 'No longer connected', 'can_message' => false];
    }
}

?>

<div class="page-header">
    <div>
        <h1>Messages</h1>
        <p>Chat privately with your <?php echo e(strtolower($contact_label)); ?>s.</p>
    </div>
</div>

<div class="card chat-layout">
    <div class="chat-contacts">
        <div class="contacts-head">
            <div class="search-input">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control form-control-sm" id="contactFilter" placeholder="Search <?php echo e(strtolower($contact_label)); ?>s…">
            </div>
        </div>
        <div class="contacts-list" id="contactsList">
            <?php if (!$contacts) { ?>
                <div class="empty-state py-4">
                    <div class="empty-icon"><i class="bi bi-people"></i></div>
                    <h6>No <?php echo e(strtolower($contact_label)); ?>s yet</h6>
                    <p><?php echo $empty_link; ?></p>
                </div>
            <?php } ?>
            <?php foreach ($contacts as $c) { $unread = $unread_by_sender[$c['id']] ?? 0; ?>
                <button type="button" class="contact-item" data-id="<?php echo intval($c['id']); ?>" data-name="<?php echo e($c['name']); ?>" data-can-message="<?php echo $c['can_message'] ? 1 : 0; ?>" data-sub="<?php echo e($c['sub']); ?>">
                    <span class="avatar sm"><?php echo e(initials($c['name'])); ?></span>
                    <span class="contact-main">
                        <span class="contact-name d-block"><?php echo e($c['name']); ?></span>
                        <span class="contact-sub d-block"><?php echo $c['can_message'] ? e($c['sub']) : '<i class="bi bi-lock"></i> ' . ($c['sub'] === 'No longer connected' ? e($c['sub']) : 'Messaging off'); ?></span>
                    </span>
                    <?php if ($unread > 0) { ?><span class="unread"><?php echo $unread; ?></span><?php } ?>
                </button>
            <?php } ?>
        </div>
    </div>

    <div class="chat-pane">
        <div class="chat-placeholder" id="chatPlaceholder">
            <div class="empty-icon mb-2" style="width:56px;height:56px;border-radius:16px;background:var(--bg-body);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="bi bi-chat-square-text"></i></div>
            <h6 class="text-body">Select a conversation</h6>
            <p class="small mb-0">Pick someone from the list to start chatting.</p>
        </div>

        <div class="chat-head" id="chatHead" style="display:none;">
            <span class="avatar sm" id="chatAvatar"></span>
            <div class="flex-grow-1" style="min-width:0;">
                <div class="fw-bold text-truncate" id="chatName"></div>
                <div class="small text-muted" id="chatSub"></div>
            </div>
            <?php if (!empty($profile_link)) { ?>
                <a class="btn btn-light btn-sm" id="chatProfileLink" href="#"><i class="bi bi-person"></i> Profile</a>
            <?php } ?>
        </div>
        <div class="chat-messages" id="chatBox" style="display:none;"></div>
        <form class="chat-compose" id="messageForm" style="display:none;">
            <textarea class="form-control" id="messageInput" rows="1" maxlength="1000" placeholder="Write a message… (Enter to send, Shift+Enter for a new line)"></textarea>
            <button class="btn btn-primary" type="submit" id="btnSend" aria-label="Send"><i class="bi bi-send-fill"></i></button>
        </form>
        <div class="notice m-3" id="chatLocked" style="display:none;">
            <i class="bi bi-lock"></i>
            <div id="chatLockedText"></div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script>
var CHAT = <?php echo json_encode([
    'selected' => $selected,
    'profileLink' => $profile_link ?? '',
    'lockedText' => $locked_text,
]); ?>;
</script>
<script src="<?php echo JS_PATH; ?>/message.js"></script>
