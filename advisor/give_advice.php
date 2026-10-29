<?php

// The Advice page was removed; advice is written and listed on each client's profile
include '../includes/config.php';
include '../includes/session.php';

redirect(ADVISOR_PATH . "/clients.php");
