<?php

$localConfig = __DIR__ . '/database.local.php';
if (is_file($localConfig)) {
    require_once $localConfig;
}

define('DB_HOST', getenv('DB_HOST') ?: (defined('LOCAL_DB_HOST') ? LOCAL_DB_HOST : 'localhost'));
define('DB_NAME', getenv('DB_NAME') ?: (defined('LOCAL_DB_NAME') ? LOCAL_DB_NAME : 'cloud_student_platform'));
define('DB_USER', getenv('DB_USER') ?: (defined('LOCAL_DB_USER') ? LOCAL_DB_USER : 'root'));
define('DB_PASS', getenv('DB_PASS') ?: (defined('LOCAL_DB_PASS') ? LOCAL_DB_PASS : ''));

function getDatabaseConnection(): ?mysqli
{
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($connection->connect_error) {
        return null;
    }

    $connection->set_charset('utf8mb4');
    return $connection;
}
