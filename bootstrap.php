<?php
/**
 * This is a place where we configure our application
 */

require_once __DIR__ . '/source/event.php';
require_once __DIR__ . '/source/router.php';


router('/', function () {
    echo "Hello World";
});

event('http.404', null, function ($path) {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 404 Not Found');
    echo sprintf("Path '%s' not found", $path);
});

