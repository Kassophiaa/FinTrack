<?php

// Account settings now live on the profile page
include '../includes/config.php';
include '../includes/session.php';

redirect(CLIENT_PATH . "/profile.php");
