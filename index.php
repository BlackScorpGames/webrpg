<?php
session_start();
error_reporting(-1);
ini_set('display_errors', true);

$scriptUrl = '/';
$beforeIndexPosition = strpos($_SERVER['PHP_SELF'], '/index.php');
if (false !== $beforeIndexPosition && $beforeIndexPosition > 0) {
    $scriptUrl = substr($_SERVER['PHP_SELF'], 0, $beforeIndexPosition).'/';
    $_SERVER['REQUEST_URI'] = str_replace(['/index.php', $scriptUrl], '/', $_SERVER['REQUEST_URI']);
}
define('BASE_DIR', $scriptUrl);

require_once __DIR__ . '/bootstrap.php';





$request =  $_SERVER['REQUEST_URI'];


router($request);
