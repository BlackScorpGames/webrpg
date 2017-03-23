<?php
function login()
{
    if (isLoggedIn()) {
        echo router('/map');
        return;
    }
    $username = filter_input(INPUT_POST, 'username',FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password',FILTER_SANITIZE_STRING);
    $errors = doLogin($username, $password);

    $data = [
        'username' => $username,
        'password' => $password,
        'errors' => $errors
    ];

    navigation(_('login'), '/');
    navigation(_('create account'), '/register');
    navigation(_('create dummy user'),'/dummyUser');
    activateNavigation('/');

    echo render('index', $data);
}

function doLogin($username, $password)
{

    if (!isPost()) {
        return [];
    }

    if (!$username || !$password) {
        return [_('Please fill Username and Password')];
    }

    $passwordHash = getPasswordHashForUsername($username);
    if (!$passwordHash) {
        return [_('User not exists')];
    }

    $passwordIsCorrect = password_verify($password, $passwordHash);
    if (!$passwordIsCorrect) {
        return [_('Invalid login')];
    }
    $userId = getUserIdForUsername($username);
    session('userId',$userId);
    session('username',$username);
    redirect('/');
    return [];
}