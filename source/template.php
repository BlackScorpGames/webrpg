<?php

function render($path, array $data = [])
{
    $templateDirectories = config('templateDirectories');

    $fileName = '';
    foreach ($templateDirectories as $templateDirectory) {
        $fileName = $templateDirectory . $path . '.php';
    }
    if (!is_file($fileName)) {
        throw new Exception(sprintf('Template file "%s" not found in directories "%s"', $path . '.php', implode('","',$templateDirectories)));
    }
    $fileName = realpath($fileName);


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

function escape($value)
{

}