<?php
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
            try {
                array_shift($match);
                return call_user_func_array($action,$match);
            } catch (Exception $exception) {
                return event('http.500', [$exception]);
            }
        }
    }
    return event('http.404', [$path, 'Route not found']);
}