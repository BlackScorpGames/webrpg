<?php
function getEquipmentForCharacter($characterName)
{
    $db = getDb();
    $characterName = mysqli_real_escape_string($db, $characterName);
    $maxEquipmentSlots = 9;
    $sql = "SELECT itemName,slot,amount FROM inventory 
            INNER JOIN characters ON(inventory.characterId = characters.characterId)
            WHERE name='" . $characterName . "' AND slot <= ".$maxEquipmentSlots."
            ORDER BY slot DESC";

    $result = mysqli_query($db, $sql);
    $equipment = array_fill(0,$maxEquipmentSlots,[
        'amount'=>0,
        'slot'=>0,
        'itemName' => ''
    ]);

    if (!$result) {
        return $equipment;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $equipment[(int)$row['slot']] = $row;
    }

    return $equipment;
}