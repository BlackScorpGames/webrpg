<?php

event('game.newCharacter', [], function ($characterId, $characterName, $class, $gender) {
    $initialLocation = config('initialLocation');
    if (!$initialLocation) {
        return;
    }
    $sql = sprintf('UPDATE characters SET map = "%s", x = %d, y = %d WHERE characterId = %d',
        $initialLocation['map'],
        $initialLocation['x'],
        $initialLocation['y'],
        $characterId
    );
    query($sql);
});


event('map.moveTo', [], function ($mapName, $newX, $newY) {
    $pathToMapFile = realpath(ROOT_DIR . '/gamedata/maps/' . $mapName . '.json');
    if (!$pathToMapFile) {
        return;
    }
    updateCharacterLocation($newX, $newY, $mapName, getSelectedCharacterName());

});