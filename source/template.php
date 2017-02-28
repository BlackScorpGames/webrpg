<?php

function render($path, array $data = [])
{

    $fileName = _getTemplateFile($path);

    _templateData('data', $data);
    if (count($data) > 0) {
        extract($data, EXTR_SKIP);
    }


    ob_clean();
    ob_start();
    try {
        require_once $fileName;
    } catch (Exception $exception) {
        ob_end_clean();
        throw $exception;
    }
    return ob_get_clean();
}

function _templateData($key, $value = null)
{
    static $data = [];

    if ($value) {
        return $data[$key] = $value;
    }

    return isset($data[$key]) ? $data[$key] : [];
}

function _getTemplateFile($path)
{
    $templateDirectories = config('templateDirectories');

    $fileName = '';
    foreach ($templateDirectories as $templateDirectory) {
        $fileName = $templateDirectory . $path . '.php';
    }
    if (!is_file($fileName)) {
        throw new Exception(sprintf('Template file "%s" not found in directories "%s"', $path . '.php', implode('","', $templateDirectories)));
    }
    return realpath($fileName);

}

function layout($path)
{
    static $layoutData = '';

    if (!(bool)$layoutData) {
        return $layoutData = $path;
    }
    $data = _templateData('data');

    echo render($path, $data);
}

function section($name)
{
    static $sections = [];

    if (!isset($sections[$name])) {
        ob_clean();
        return $sections[$name] = ob_start();
    }
    $content = ob_get_clean();
    $data = _templateData('data');
    $data[$name] = $content;
    _templateData('data',$data);
    unset($sections[$name]);
    return true;
}

function sectionAppend($name)
{
    static $sections = [];

    if (!isset($sections[$name])) {
        ob_clean();
        return $sections[$name] = ob_start();
    }
    $content = ob_get_clean();
    $data = _templateData('data');
    $data[$name] =   $data[$name].$content;
    _templateData('data',$data);
    unset($sections[$name]);
    return true;
}

function escape($value)
{
    return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
}