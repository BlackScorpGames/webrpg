<?php
/**
 * This is a place where we configure our application
 */
define('ROOT_DIR', realpath(__DIR__));

if (!extension_loaded('gettext')) {
    $message = sprintf("Missing gettext extension, please modify '%s' and enable the gettext extension, afterwards restart server", php_ini_loaded_file());
    die($message);
}
/**
 * Include base classes
 */
require_once __DIR__ . '/source/functions.php';
require_once __DIR__ . '/source/template.php';

/**
 * Include configuration files
 */
require_once __DIR__ . '/config/default.php';

$databaseFile = __DIR__ . '/config/database.php';
if (!is_file($databaseFile)) {
    $message = sprintf("File '%s' is missing please copy and rename '%s' to '%s'", $databaseFile, __DIR__ . str_replace('database.php', 'database.example.php', $databaseFile), $databaseFile);
    die($message);
}
require_once $databaseFile;

/**
 * Include all configured modules
 */
require_once __DIR__ . '/config/modules.php';

router('/assets/(.*)', function ($path) {
    $filePath = realpath(ROOT_DIR . '/assets/' . $path);
    if (!$filePath) {
        header("HTTP/1.1 404 Not Found");
        http_response_code(404);
        return;
    }
    $mimeType = mime_content_type($filePath);
    if ($mimeType === 'text/plain') {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypeForExtensions = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'svg'=>'image/svg+xml'
        ];
        if(isset($mimeTypeForExtensions[$extension])){
            $mimeType = $mimeTypeForExtensions[$extension];
        }

    }

    header('Content-Type:' . $mimeType);
    ob_end_clean();
    return readfile($filePath);
});

set_error_handler(function () {
    event('http.500', ['message' => func_get_arg(1), 'context' => func_get_arg(4)]);
}, E_ALL & ~E_WARNING);

/**
 * Setup basic events
 */
event('http.403', [], function () {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 403 Forbidden');
    echo 'You are not logged in, please <a href="/login">login</a> first';
});

event('http.404', [], function ($path) {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 404 Not Found');
    echo sprintf("Path '%s' not found", $path);
});

event('http.500', [], function ($message, $context) {
    header('Content-Type:text/html;charset=utf-8');
    header('HTTP/1.0 500 Internal Server Error');
    echo sprintf('Something went wrong, got exception with message "<b style="color:indianred">%s</b>" <pre>%s</pre>', $message, print_r($context, true));
});

