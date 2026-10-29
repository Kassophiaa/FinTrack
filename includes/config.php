<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Work out the public URL of the project folder so the app runs from any
// location under the web root (e.g. /FinTrack or /Complete Projects/php/FinTrack).
function detectBaseUrl() {
    $docRoot = str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '', '/\\'));
    $project = str_replace('\\', '/', dirname(__DIR__));

    $path = '/FinTrack';
    if ($docRoot !== '' && stripos($project, $docRoot) === 0) {
        $path = substr($project, strlen($docRoot));
    }
    $path = implode('/', array_map('rawurlencode', explode('/', $path)));

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . $path;
}

$base_url = detectBaseUrl();
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
