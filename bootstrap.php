<?php
/**
 * This is a place where we configure our application
 */

/**
 * Include base classes
 */
require_once __DIR__ . '/source/event.php';
require_once __DIR__ . '/source/router.php';
require_once __DIR__ . '/source/template.php';
require_once __DIR__ . '/source/config.php';

/**
 * Include configuration files
 */

require_once __DIR__ . '/config/default.php';

/**
 * Enable modules
 */
require_once __DIR__ . '/source/account/index.php'; //include account module
require_once __DIR__ . '/source/map/index.php'; //include map module

router('/', function () {
    echo render('index');
});

/**
 * Setup basic events
 */
event('http.403', [], function () {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 403 Forbidden');
    echo 'You are not logged in, please <a href="login">login</a> first';
});

event('http.404', [], function ($path) {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 404 Not Found');
    echo sprintf("Path '%s' not found", $path);
});

event('http.500', [], function (Exception $exception) {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 500 Internal Server Error');
    echo sprintf("Something went wrong, got exception with message '%s'", $exception->getMessage());
});