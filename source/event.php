<?php

function event($name, array $data = null, $action = null)
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