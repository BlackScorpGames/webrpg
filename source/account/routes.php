<?php

router('/', 'login');

router('/login', 'login');

router('/dummyUser', function () {
    list($username, $password, $email) = array_values(config('dummyUser'));
    if(createUser($username, $password, $email)){
        echo "User created";
        return;
    }
    echo "Failed to create user";
});