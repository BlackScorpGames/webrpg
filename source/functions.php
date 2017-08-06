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

function isAjax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'];
}
/**
 * @param string $path
 * @return null
 */
function redirect($path)
{
    if($path === '/'){
        $path = BASE_DIR;
    }
    header('Location:' . $path);

    return null;
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

    $mysqli = mysqli_init();
    /**
     * we need to add @ for mysqli_real_connect because of MAMMP PRO 3.3.0 it shows a warning
     */
    $connectionResult = @mysqli_real_connect($mysqli, $host, $user, $password, $database, $port);

    if (!$connectionResult) {
        trigger_error(mysqli_connect_error());
        return null;
    }
    mysqli_set_charset($mysqli, $charset);

    return $mysqli;
}

/**
 * @param mysqli|null $db
 * @return string
 */
function getDbError(mysqli $db = null)
{
    if (is_null($db)) {
        $db = getDb();
    }

    return mysqli_error($db);
}

/**
 * @param string $sql
 * @param mysqli|null $db
 * @param int $resultMode
 * @return bool|mysqli_result
 */
function query($sql, mysqli $db = null, $resultMode = MYSQLI_STORE_RESULT)
{
    if (is_null($db)) {
        $db = getDb();
    }

    return mysqli_query($db, $sql, $resultMode);
}

/**
 * @param string $text
 * @param mysqli|null $db
 * @return string
 */
function queryEscape($text, mysqli $db = null)
{
    if (is_null($db)) {
        $db = getDb();
    }

    return mysqli_real_escape_string($db, $text);
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
 * @param string $name
 * @param string|null $value
 *
 * @return mixed
 */
function session($name, $value = null)
{
    if (!$value && func_num_args() === 2) {
        unset($_SESSION[$name]);

        return null;
    }
    if ($value) {
        return $_SESSION[$name] = $value;
    }

    return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
}

/**
 * @param string $name
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

function loadAsset($path)
{

}

/**
 * @param string $name
 * @param array $data
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
 * @param string $key
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
    return require_once __DIR__ . '/../config/badwords.php';
}

/**
 * @param string $path
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
            event('middleware.before.' . $route, [$match]);
            call_user_func_array($action, $match);
            event('middleware.after.' . $route, [$match]);
            event('middleware.after', [$match]);

            return null;
        }
    }

    return event('http.404', [$path, 'Route not found']);
}

/**
 * @param string|null $name
 * @param string|null $bootstrapFile
 *
 * @return string|array|null
 */
function module($name = null, $bootstrapFile = null)
{
    static $modules = [];

    if (is_null($name)) {
        return $modules;
    }

    if (is_null($bootstrapFile)) {
        if (!isset($modules[$name])) {
            $message = sprintf('Unknown module "%s".', $name);

            trigger_error($message);
            return null;
        }

        return $modules[$name];
    }

    if (isset($modules[$name])) {
        $message = sprintf('Module "%s" already loaded.', $name);
        trigger_error($message);

        return null;
    }
    $modulePath = config('moduleFolder') . '/' . $name . '/' . $bootstrapFile;
    if (!is_file($modulePath)) {
        $message = sprintf('Module "%s" not exists.', $name);
        trigger_error($message);

        return null;
    }
    event('module.before', [$name, $bootstrapFile]);
    require_once $modulePath;
    event('module.after', [$name, $bootstrapFile]);

    return $modules[$name] = $modulePath;
}
