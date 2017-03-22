<?php

router('/testNewChar', function () {
    $newCharacter = [
        'id' => 1,
        'name' => 'foo',
        'class' => 'archer',
        'gender' => 'male'
    ];
    event('game.newCharacter', $newCharacter);
});

event('game.newCharacter', [], function ($characterId, $characterName, $class, $gender) {
    $key = sprintf('%s.%s', $gender, $class);
    $initialEquipmentConfig = config('initialEquipment');

    if (!isset($initialEquipmentConfig[$key])) {
        return;
    }
    $initialEquipment = $initialEquipmentConfig[$key];
    $db = getDb();
    foreach ($initialEquipment as $slot => $itemName) {
        $sql = "INSERT INTO inventory(characterId,slot,itemName,amount) VALUES(".$characterId.",".$slot.",'".$itemName."',1)";
        mysqli_query($db,$sql);
    }
    var_dump($initialEquipment);
});