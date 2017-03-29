<?php
event('game.newCharacter', [], function ($characterId, $characterName, $class, $gender) {

    $initialLocation = config('initialLocation');
    if (!$initialLocation) {
        return;
    }
    $map = $initialLocation['map'];
    $x = $initialLocation['x'];
    $y = $initialLocation['y'];

    $sql = "UPDATE characters SET map = '" . $map . "',x = " . $x . ",y=" . $y . " WHERE characterId = " . $characterId;
    $db = getDb();
    mysqli_query($db, $sql);

});