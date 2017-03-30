<?php

/**
 * @param string $characterName
 * @return array
 */
function getEquipmentForCharacter($characterName)
{
    $maxEquipmentSlots = count(config('equipmentSlots'));
    $sql = sprintf('SELECT itemName, slot, amount FROM inventory INNER JOIN characters ON inventory.characterId = characters.characterId WHERE name="%s" AND slot <= %d ORDER BY slot DESC',
        queryEscape($characterName),
        $maxEquipmentSlots
    );
    $result = query($sql);
    $equipment = array_fill(0, $maxEquipmentSlots, [
        'amount' => 0,
        'slot' => 0,
        'itemName' => ''
    ]);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $equipment[(int)$row['slot']] = $row;
        }
    }

    return $equipment;
}
