<?php
function config($key, $value = null)
{
    static $config = [];
    if ($value) {
        return $config[$key] = $value;
    }
    return isset($config[$key]) ? $config[$key] : null;
}