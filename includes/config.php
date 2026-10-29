<?php

session_start();

$base_url = "http://localhost/finance_web";
$base_path = dirname(__DIR__);

define('BASE_URL', $base_url);
define('BASE_PATH', $base_path);

define('DB_PATH', BASE_PATH . "/db");
define('CLASSES_PATH', BASE_PATH . "/classes");
define('CSS_PATH', BASE_URL . "/css");
define('JS_PATH', BASE_URL . "/js");

define('ADMIN_PATH', BASE_URL . "/admin");
define('ADVISOR_PATH', BASE_URL . "/advisor");
define('CLIENT_PATH', BASE_URL . "/client");

require_once __DIR__ . '/functions.php';

?>
