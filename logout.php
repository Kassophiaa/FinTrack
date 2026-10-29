<?php

include 'includes/config.php';

session_unset();
session_destroy();

redirect(BASE_URL . "/index.php");

?>
