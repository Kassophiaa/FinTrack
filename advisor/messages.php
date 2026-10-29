<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/AdvisorPermission.php';
require_once '../classes/Message.php';

$active = "messages";

$contacts = [];
$res = (new AdvisorPermission())->getPermittedClientsForAdvisor($_SESSION['user_id']);
while ($res && ($row = $res->fetch_assoc())) {
    $contacts[] = ['id' => $row['client_id'], 'name' => $row['full_name'], 'sub' => '@' . $row['username'], 'can_message' => $row['can_message'] == 1];
}
$contact_label = "Client";
$empty_link = 'Clients appear here once they share access with you.';
$profile_link = "client_profile.php?user_id=";
$locked_text = "This client hasn't enabled messaging with you.";

$title = "Messages";
include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/messages_page.php';
