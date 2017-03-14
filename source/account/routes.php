<?php

router('/', 'login');

router('/login', 'login');

router('/dummyUser', function () {
    list($username, $password, $email) = array_values(config('dummyUser'));
    if (createUser($username, $password, $email)) {
        echo sprintf('User %s created with password %s',$username,$password);
        return;
    }
    echo "Failed to create user";
});

router('/logout', function () {
    session_regenerate_id(true);
    session_destroy();
    redirect('/');
});