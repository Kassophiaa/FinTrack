<?php

include '../includes/config.php';
include '../includes/session.php';

require_once '../classes/AdvisorPermission.php';
require_once '../classes/Message.php';

$active = "messages";

$contacts = [];
$res = (new AdvisorPermission())->getPermittedAdvisorsForClient($_SESSION['user_id']);
while ($res && ($row = $res->fetch_assoc())) {
    $contacts[] = ['id' => $row['advisor_id'], 'name' => $row['full_name'], 'sub' => 'Advisor', 'can_message' => $row['can_message'] == 1];
}
$contact_label = "Advisor";
$empty_link = 'Connect with an advisor to start messaging. <a href="advisors.php">Find an advisor</a>';
$profile_link = "advisor_profile.php?advisor_id=";
$locked_text = "Messaging is turned off for this advisor. You can turn it on in their access settings.";

$title = "Messages";
include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/messages_page.php';
