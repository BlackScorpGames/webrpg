<?php

/**
 * @return void
 */
function registration()
{
    if (isLoggedIn()) {
        echo router('/');
        return;
    }
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $errors = [];
	if(isPost()){
		$errors = doRegister($username, $password, $email);
	}
    

    $data = [
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'errors' => $errors
    ];

    navigation(_('login'), '');
    navigation(_('create account'), 'register');
    activateNavigation('register');

    echo render('registration', $data);
}

/**
 * @param string $username
 * @param string $password
 * @param string $email
 * @return array
 */
function doRegister($username, $password, $email )
{

    if (!$username || !$password || !$email) {
        return [_('Please fill Username or Password or Email')];
    }

    createUser($username, $password, $email);
    redirect('/');

    return [];
}
