<?php
/**
 * This is a place where we configure our application
 */

/**
 * Include base classes
 */
require_once __DIR__ . '/source/functions.php';
require_once __DIR__ . '/source/template.php';


/**
 * Include configuration files
 */

require_once __DIR__ . '/config/default.php';

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

router('/', function () {
    $data = [
        'title' => "Welcome",
        'variable' => 'foo & bar<br/>'
    ];
    echo render('index', $data);
});


/**
 * Enable modules
 */
require_once __DIR__ . '/source/account/index.php'; //include account module
require_once __DIR__ . '/source/map/index.php'; //include map module

