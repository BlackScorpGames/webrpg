<?php

router('/testNewChar',function(){
    $newCharacter = [
        'name' => 'foo',
        'class' => 'ranger',
        'gender' => 'male'
    ];
    event('game.newCharacter',$newCharacter);
});

event('game.newCharacter', [], function ($characterName,$class,$gender) {
var_dump(func_get_args());

die();
});