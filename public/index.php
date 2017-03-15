<?php
session_start();
error_reporting(-1);
ini_set('display_errors', true);

require_once __DIR__ . '/../bootstrap.php';

$request = str_replace('index.php/','', $_SERVER['REQUEST_URI'] );
router($request);