<?php
/**
 * @return bool is post request
 */
function isPost()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * @return bool is get request
 */
function isGet()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * @param string $path
 */
function redirect($path)
{
    header('Location:'.$path);
    return;
}

/**
 * @return mysqli | null
 */
function getDb()
{
    /**
     * @var mysqli
     */
    static $mysqli = null;

    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    list($host, $user, $password, $database, $port, $charset) = array_values(config('db'));
    $mysqli = mysqli_connect($host, $user, $password, $database, $port);

    if (mysqli_connect_error()) {
        trigger_error(mysqli_connect_error());
        return null;
    }
    mysqli_set_charset($mysqli, $charset);

    return $mysqli;
}

/**
 * @param string $url
 */
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

/**
 * @param string|null $title
 * @param string|null $url
 *
 * @return mixed
 */
function navigation($title = null, $url = null)
{
    if ($title) {
        $item = [
            'title' => $title,
            'url' => $url,
            'isActive' => false,
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

/**
 * @param string      $name
 * @param string|null $value
 *
 * @return mixed
 */
function session($name, $value = null)
{
    if (!$value && func_num_args() === 2) {
        unset($_SESSION[$name]);

        return;
    }
    if ($value) {
        return $_SESSION[$name] = $value;
    }

    return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
}

/**
 * @param string      $name
 * @param string|null $value
 *
 * @return mixed
 */
function sharedVariable($name, $value = null)
{
    static $variables = [];
    if ($value) {
        return $variables[$name] = $value;
    }

    return isset($variables[$name]) ? $variables[$name] : null;
}

/**
 * @param string     $name
 * @param array      $data
 * @param mixed|null $action
 *
 * @return mixed
 */
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

/**
 * @param string      $key
 * @param string|null $value
 *
 * @return mixed
 */
function config($key, $value = null)
{
    static $config = [];
    if ($value) {
        return $config[$key] = $value;
    }

    return isset($config[$key]) ? $config[$key] : null;
}

/**
 * @return array
 */
function getBadWords()
{
    return require_once __DIR__.'/../config/badwords.php';
}

/**
 * @param string               $path
 * @param closure|string|array $action
 *
 * @return mixed
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
            event('middleware.before', [$match]);
            event('middleware.before.'.$route, [$match]);
            call_user_func_array($action, $match);
            event('middleware.after.'.$route, [$match]);
            event('middleware.after', [$match]);

            return null;
        }
    }

    return event('http.404', [$path, 'Route not found']);
}