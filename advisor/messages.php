<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advisor.php';
require_once '../classes/Message.php';

$active = "messages";

require_once '../classes/AdvisorPermission.php';
$advisor = new Advisor();
$perm = new AdvisorPermission();

$message = new Message();
$unread = $message->getUnreadCount($_SESSION['user_id']);

$title = "Messages";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_advisor.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-chat-dots" style="color: var(--primary);"></i> Messages</h4>
        </div>

        <div id="alert"></div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-body">
                        <label class="form-label"><i class="bi bi-person"></i> Select Client</label>
                        <select class="form-select" id="clientSelect">
                            <option value="">-- Select Client --</option>
                            <?php
                            $permittedClients = $perm->getPermittedClientsForAdvisor($_SESSION['user_id']);
                            if ($permittedClients && $permittedClients->num_rows > 0) {
                                while ($prow = $permittedClients->fetch_assoc()) {
                                    echo '<option value="' . $prow['client_id'] . '">' . $prow['full_name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <span class="badge bg-danger me-2" style="font-size: 0.9rem;"><?php echo $unread; ?> unread</span>
                <button class="btn btn-primary" id="btnLoad"><i class="bi bi-chat-dots"></i> Load Conversation</button>
            </div>
        </div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-chat-left-text"></i> Conversation</div>
            <div class="card-body">
                <div id="chatBox" style="height: 400px; overflow-y: auto; border: 1px solid var(--border-color); padding: 1rem; background: var(--bg-body); border-radius: var(--radius-sm);">
                    <p class="text-muted text-center" style="padding-top: 180px;"><i class="bi bi-chat-left-dots"></i> Select a client to load conversation</p>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="card-body">
                <form id="messageForm">
                    <input type="hidden" id="receiver_id" value="">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pencil"></i></span>
                        <input type="text" class="form-control" id="messageInput" placeholder="Type your message..." disabled>
                        <button class="btn btn-primary" type="submit" id="btnSend" disabled><i class="bi bi-send"></i> Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/message.js"></script>
