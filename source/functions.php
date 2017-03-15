<?php
function isPost()
{
    return filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST';
}

function isGet()
{
    return filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET';
}

function redirect($path)
{
    header('Location:' . $path);
    return;
}

/**
 * @return mysqli
 */
function getDb()
{
    /**
     * @var mysqli
     */
    static $mysqli = null;
    if ($mysqli) {
        return $mysqli;
    }

    list($host, $user, $password, $database, $port, $charset) = array_values(config('db'));
    $mysqli = mysqli_connect($host, $user, $password, $database, $port);

    if (mysqli_connect_error()) {
        trigger_error(mysqli_connect_error());
    }
    mysqli_set_charset($mysqli, $charset);
    return $mysqli;
}

function activateNavigation($url)
{
    $navigationItems = sharedVariable('navigation');
    foreach ($navigationItems as $key => $navigationItem) {
        $navigationItems[$key]['isActive'] = false;
    }
    foreach ($navigationItems as $key => $navigationItem) {
        if ($navigationItem['url'] === $url) {
            $navigationItems[$key]['isActive'] = true;
        }
    }

    sharedVariable('navigation', $navigationItems);
}

function navigation($title = null, $url = null)
{
    if ($title) {
        $item = [
            'title' => $title,
            'url' => $url,
            'isActive' => false
        ];
        $items = sharedVariable('navigation');
        if (!$items) {
            $items = [];
        }
        $items[] = $item;

        return sharedVariable('navigation', $items);
    }
    $navigation = sharedVariable('navigation');
    if (!$navigation) {
        return [];
    }
    return $navigation;
}

function sharedVariable($name, $value = null)
{
    static $variables = [];
    if ($value) {
        return $variables[$name] = $value;
    }
    return isset($variables[$name]) ? $variables[$name] : null;
}

function event($name, array $data = [], $action = null)
{
    static $events = [];

    if ($action) {

        return $events[$name][] = $action;
    }

    if (isset($events[$name])) {
        foreach ($events[$name] as $event) {
            call_user_func_array($event, $data);
        }
    }
    return null;
}

function config($key, $value = null)
{
    static $config = [];
    if ($value) {
        return $config[$key] = $value;
    }
    return isset($config[$key]) ? $config[$key] : null;
}

function getBlackList()
{
    return require_once __DIR__ . '/../config/blacklist.php';
}

/**
 * @param $path
 * @param closure|string|array $action
 * @return null
 */
function router($path, $action = null)
{
    static $routes = [];
    if (!$path) {
        $path = '/';
    }
    if ($action) {
        return $routes[$path] = $action;
    }

    foreach ($routes as $route => $action) {
        $match = [];

        if (preg_match("~^$route$~", $path, $match)) {
            if (!is_callable($action)) {
                return event('http.404', [$path, 'Route not found']);
            }
            array_shift($match);
            return call_user_func_array($action, $match);
        }
    }
    return event('http.404', [$path, 'Route not found']);
}