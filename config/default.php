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
config('dummyUser', [
    'username' => 'test',
    'password' => 'test',
    'email' => 'test@test.com'
]);

config('equipmentSlots', [
    0 => 'head',
    1 => 'armor',
    2 => 'weapon-right',
    3 => 'weapon-left',
    4 => 'ring-left',
    5 => 'ring-right'
]);

config('initialEquipment', [
    'male.warrior' => [
        0 => 'plateArmor',
        1 => 'plateArmor',
        2 => 'sword',
        3 => 'shield'
    ],
    'female.warrior' => [
        0 => 'plateArmor',
        1 => 'plateArmor',
        2 => 'sword',
        3 => 'shield'
    ],
    'male.archer' => [
        0 => 'leatherArmor',
        1 => 'leatherArmor',
        2 => 'bow'
    ],
    'female.archer' => [
        0 => 'leatherArmor',
        1 => 'leatherArmor',
        2 => 'bow'
    ],
    'male.mage' => [
        0 => 'clothing',
        1 => 'clothing',
        2 => 'staff',
    ],
    'female.mage' => [
        0 => 'clothing',
        1 => 'clothing',
        2 => 'staff',
    ],
]);