<?php
function isLoggedIn()
{
    return session('username') !== null;
}

function getCurrentUsername()
{
    if (isLoggedIn()) {
        return session('username');
    }
    return '';
}
function getCurrentUserId(){
    if (isLoggedIn()) {
        return (int)session('userId');
    }
    return 0;
}

function redirectIfNotLoggedIn(){
    if (!isLoggedIn()) {
        redirect('/');
    }
}
function getUserIdForUsername($username)
{
    $db = getDb();
    $sql = "SELECT userId FROM users WHERE username = '" . mysqli_real_escape_string($db, $username) . "'";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        trigger_error(mysqli_error($db));
        return null;
    }
    return (int)mysqli_fetch_row($result)[0];
}

function getPasswordHashForUsername($username)
{
    $db = getDb();
    $sql = "SELECT password FROM users WHERE username = '" . mysqli_real_escape_string($db, $username) . "'";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        trigger_error(mysqli_error($db));
        return null;
    }
    return mysqli_fetch_row($result)[0];
}

function createUser($username, $password, $email)
{
    $db = getDb();
    $username = mysqli_real_escape_string($db, $username);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $email = mysqli_real_escape_string($db, $email);

    $sql = "INSERT INTO users (username,password,email,registrationDate) VALUES('" . $username . "','" . $password . "','" . $email . "',NOW())";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        trigger_error(mysqli_error($db));
        return false;
    }
    return $result;
}