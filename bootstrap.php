<?php
/**
 * This is a place where we configure our application
 */

require_once __DIR__ . '/source/event.php';
require_once __DIR__ . '/source/router.php';

require_once __DIR__ . '/source/account/index.php'; //include account module
require_once __DIR__ . '/source/map/index.php'; //include map module

router('/', function () {
    echo "Hello World";
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

