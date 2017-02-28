<?php
function login()
{
    $username = filter_input(INPUT_POST, 'username');
    $password = filter_input(INPUT_POST, 'password');
    $data = [
        'username' => $username,
        'password' => $password
    ];
    $db = getDb();
    echo render('index', $data);
}