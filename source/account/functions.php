<?php

/**
 * @return bool
 */
function isLoggedIn()
{
    return session('username') !== null;
}

/**
 * @return mixed|string
 */
function getCurrentUsername()
{
    if (isLoggedIn()) {
        return session('username');
    }

    return '';
}

/**
 * @return int
 */
function getCurrentUserId()
{
    if (isLoggedIn()) {
        return (int)session('userId');
    }

    return 0;
}

/**
 * @return void
 */
function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        redirect('/');
    }
}

/**
 *@return int|null
 */
function getUserIdForUsername($username)
{
    $sql = sprintf('SELECT userId FROM users WHERE username = "%s"',
        queryEscape($username)
    );
    $result = query($sql);
    if (!$result) {
        trigger_error(getDbError());
        return null;
    }

    return (int)mysqli_fetch_row($result)[0];
}

/**
 * @param string $username
 * @return null|string
 */
function getPasswordHashForUsername($username)
{
    $sql = sprintf('SELECT password FROM users WHERE username = "%s"',
        queryEscape($username)
    );
    $result = query($sql);
    if (!$result) {
        trigger_error(getDbError());
        return null;
    }

    return mysqli_fetch_row($result)[0];
}

/**
 * @param string $username
 * @param string $password
 * @param string $email
 * @return bool|mysqli_result
 */
function createUser($username, $password, $email)
{
    $sql = sprintf('INSERT INTO users (username, password, email, registrationDate) VALUES("%s", "%s", "%s", NOW())',
        queryEscape($username),
        password_hash($password, PASSWORD_DEFAULT),
        queryEscape($email)
    );
    $result = query($sql);
    if (!$result) {
        trigger_error(getDbError());
        return false;
    }

    return $result;
}
