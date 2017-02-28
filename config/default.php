<?php

config('templateDirectories',
    [
        __DIR__ . '/../templates/default/',
        __DIR__ . '/../templates/'
    ]
);

/**
 * Config to generate a dummy user without registration by calling http://localhost/dummyUser
 */
config('dummyUser',[
    'username' => 'test',
    'password' => 'test',
    'email' => 'test@test.com'
]);
