<?php

/**
 * @param string  $path
 * @param mixed[] $data
 *
 * @return string
 */
function render($path, array $data = [])
{
    $fileName = _getTemplateFile($path);

    _templateData('data', $data);
    if (count($data) > 0) {
        extract($data, EXTR_SKIP);
    }

    ob_start();
    require_once $fileName;

    return ob_get_clean();
}

/**
 * @param string $key
 * @param mixed  $value
 *
 * @return mixed
 */
function _templateData($key, $value = null)
{
    static $data = [];

    if ($value) {
        return $data[$key] = $value;
    }

    return isset($data[$key]) ? $data[$key] : [];
}

/**
 * @param string $path
 *
 * @return string
 */
function _getTemplateFile($path)
{
    $templateDirectories = config('templateDirectories');

    $fileName = '';
    foreach ($templateDirectories as $templateDirectory) {
        $fileName = $templateDirectory.$path.'.php';
        if (is_file($fileName)) {
            return realpath($fileName);
        }
    }
    if (!(bool)$fileName) {
        trigger_error(
            sprintf(
                'Template file "%s" not found in directories "%s"',
                $path.'.php',
                implode('","', $templateDirectories)
            )
        );
    }

    return $fileName;
}

/**
 * @param string $path
 *
 * @return mixed
 */
function layout($path)
{
    static $layoutData = '';

    if (!(bool)$layoutData) {
        return $layoutData = $path;
    }
    $data = _templateData('data');

    echo render($path, $data);

    return null;
}

/**
 * @param string $name
 *
 * @return bool
 */
function section($name)
{
    static $sections = [];

    if (!isset($sections[$name])) {
        return $sections[$name] = ob_start();
    }
    $content = ob_get_clean();
    $data = _templateData('data');
    $data[$name] = $content;
    _templateData('data', $data);
    unset($sections[$name]);

    return true;
}

/**
 * @param string $name
 *
 * @return bool
 */
function sectionAppend($name)
{
    static $sections = [];

    if (!isset($sections[$name])) {
        ob_clean();

        return $sections[$name] = ob_start();
    }
    $content = ob_get_clean();
    $data = _templateData('data');
    $data[$name] = $data[$name].$content;
    _templateData('data', $data);
    unset($sections[$name]);

    return true;
}

/**
 * @param string $value
 *
 * @return string
 */
function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
