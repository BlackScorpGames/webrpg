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

config('equipmentSlots',[
    0 => 'head',
    1 => 'torso',
    2 => 'shoulders',
    3 => 'hands',
    4 => 'belt',
    5 => 'legs',
    6 => 'feet',
    7 => 'weapon-right',
    8 => 'weapon-left'
]);