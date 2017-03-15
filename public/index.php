<?php
session_start();
error_reporting(-1);
ini_set('display_errors', true);

require_once __DIR__ . '/../bootstrap.php';
$request = filter_input(INPUT_SERVER,'REQUEST_URI');
$request = str_replace('index.php/','',$request);

router($request);