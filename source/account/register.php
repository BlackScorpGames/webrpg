<?php

/**
 * @return void
 */
function Registration()
{
    if (isLoggedIn()) {
        echo router('/register');
        return;
    }
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $errors = doRegistr($username, $password, $email);

    $data = [
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'errors' => $errors
    ];

    navigation(_('login'), '/');
    activateNavigation('/');

    echo render('registration', $data);
}

/**
 * @param string $username
 * @param string $password
 * @param string $email
 * @return array
 */
function doRegistr($username, $password, $email )
{
    if (!isPost()) {
        return [];
    }

    if (!$username || !$password || !$email) {
        return [_('Please fill Username or Password or Email')];
    }

    createUser($username, $password, $email);
    redirect('/');

    return [];
}
