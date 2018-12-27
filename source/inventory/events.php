<?php

event('game.newCharacter', [], function ($characterId, $characterName, $class, $gender) {
    $key = sprintf('%s.%s', $gender, $class);
    $initialEquipmentConfig = config('initialEquipment');
    if (!isset($initialEquipmentConfig[$key])) {
        return;
    }
    $initialEquipment = $initialEquipmentConfig[$key];
    foreach ($initialEquipment as $slot => $itemName) {
        $sql = sprintf('INSERT INTO inventory(characterId, slot, itemName, amount) VALUES(%d, %d, "%s", 1)',
            $characterId,
            $slot,
            $itemName
        );
        query($sql);
    }
});
