<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/Advisor.php';
require_once '../classes/User.php';
require_once '../classes/Message.php';

$active = "messages";

$advisorClass = new Advisor();
$advisors = $advisorClass->getAdvisors();

$message = new Message();
$unread = $message->getUnreadCount($_SESSION['user_id']);

$title = "Messages";
include '../includes/header.php';
include '../includes/navbar.php';

?>

<div class="row">
    <div class="col-md-3">
        <?php include '../includes/sidebar_client.php'; ?>
    </div>
    <div class="col-md-9">

        <div class="page-header">
            <h4><i class="bi bi-chat-dots" style="color: var(--primary);"></i> Messages</h4>
        </div>

        <div id="alert"></div>

        <div class="form-card mb-4">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Select Advisor</label>
                        <select class="form-select" id="advisorSelect">
                            <option value="">-- Select Advisor --</option>
                            <?php
                            require_once '../classes/AdvisorPermission.php';
                            $permClass = new AdvisorPermission();
                            $permittedAdvisors = $permClass->getPermittedAdvisorsForClient($_SESSION['user_id']);
                            if ($permittedAdvisors && $permittedAdvisors->num_rows > 0) {
                                while ($row = $permittedAdvisors->fetch_assoc()) {
                                    echo '<option value="' . $row['advisor_id'] . '">' . $row['full_name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-7 d-flex align-items-end gap-2">
                        <?php if ($unread > 0) { ?>
                            <span class="badge" style="background: var(--danger); color: #fff;"><?php echo $unread; ?> unread</span>
                        <?php } ?>
                        <button class="btn btn-dark" id="btnLoad"><i class="bi bi-chat-dots"></i> Load Conversation</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card mb-4">
            <div class="card-header"><i class="bi bi-chat-left-text"></i> Conversation</div>
            <div class="card-body">
                <div id="chatBox" class="chat-box">
                    <p class="text-muted text-center">Select an advisor to load conversation</p>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="card-body">
                <form id="messageForm">
                    <input type="hidden" id="receiver_id" value="">
                    <div class="input-group chat-input">
                        <input type="text" class="form-control" id="messageInput" placeholder="Type your message..." disabled>
                        <button class="btn btn-dark" type="submit" id="btnSend" disabled><i class="bi bi-send"></i> Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?php echo JS_PATH; ?>/message.js"></script>
