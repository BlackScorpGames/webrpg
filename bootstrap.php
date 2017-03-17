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

if (!is_file(__DIR__ . '/config/database.php')) {
    $message = sprintf("File '%s' is missing please copy and rename '%s' to '%s'", __DIR__ . '/config/database.php', __DIR__ . '/config/database.example.php', __DIR__ . '/config/database.php');
    die($message);
}
require_once __DIR__ . '/config/database.php';

set_error_handler(function () {
    event('http.500', ['message' => func_get_arg(1), 'context' => func_get_arg(4)]);
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

event('http.500', [], function ($message,$context) {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 500 Internal Server Error');
    echo sprintf("Something went wrong, got exception with message '%s' <pre>%s</pre>", $message,print_r($context,true));
});
router('/', function () {
    echo "Hello world!";
});


/**
 * Enable modules
 */
require_once __DIR__ . '/source/account/index.php'; //include account module
require_once __DIR__ . '/source/character/index.php'; //include character module
require_once __DIR__ . '/source/inventory/index.php'; //include inventory module
require_once __DIR__ . '/source/map/index.php'; //include map module

