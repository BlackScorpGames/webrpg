<?php
function login()
{
    $username = filter_input(INPUT_POST, 'username');
    $password = filter_input(INPUT_POST, 'password');
    $data = [
        'username' => $username,
        'password' => $password
    ];
    echo render('index', $data);
}