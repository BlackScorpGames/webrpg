<?php
session_start();
error_reporting(-1);
ini_set('display_errors', true);

require_once __DIR__ . '/../bootstrap.php';
router(filter_input(INPUT_SERVER,'PATH_INFO'));