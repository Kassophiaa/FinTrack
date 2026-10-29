<?php

// The "My Advice" page was removed; advice is shown on each advisor's profile and the dashboard
include '../includes/config.php';
include '../includes/session.php';

redirect(CLIENT_PATH . "/dashboard.php");
