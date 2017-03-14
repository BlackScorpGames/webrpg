<?php
function isLoggedIn()
{
    return isset($_SESSION['username']);
}

function getCurrentUsername()
{
    if (isLoggedIn()) {
        return $_SESSION['username'];
    }
    return '';
}

function getPasswordHashForUsername($username)
{
    $db = getDb();
    $sql = "SELECT password FROM users WHERE username = '" . mysqli_real_escape_string($db, $username) . "'";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        trigger_error(mysqli_error($db), E_USER_ERROR);
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
        trigger_error(mysqli_error($db), E_USER_ERROR);
        return false;
    }
    return $result;
}