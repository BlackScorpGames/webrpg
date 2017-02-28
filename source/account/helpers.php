<?php
function isLoggedIn()
{
    return isset($_SESSION['username']);
}

function getPasswordHashForUsername($username)
{
    $db = getDb();
    $sql = "SELECT passwordHash FROM users WHERE username = " . mysqli_real_escape_string($db, $username);
    $statement = mysqli_query($db, $sql);
    if (!$statement) {
        return null;
    }
    return mysqli_fetch_row($statement);
}