<?php

/**
 * @return bool
 */
function isCharacterSelected()
{
    return session('characterName') !== null;
}

/**
 * @return mixed
 */
function getSelectedCharacterName()
{
    return session('characterName');
}

/**
 * @param null|string $character
 * @return array|null
 */
function initializeCharacterData($character = null)
{
    $characters = getCharactersForUser(getCurrentUsername());
    if (count($characters) === 0) {
        router('/character/new');

        return null;
    }
    $activeCharacter = array_values($characters)[0];
    if ($character) {
        $key = md5($character);
        $activeCharacter = isset($characters[$key]) ? $characters[$key] : $activeCharacter;
    }
    $activeCharacter['inventory'] = getEquipmentForCharacter($activeCharacter['name']);

    navigation(_('Select character'), '/character/view');
    navigation(_('Logout'), '/logout');

    activateNavigation('/character/select');

    return [$characters, $activeCharacter];
}

/**
 * @param string $direction
 * @return mixed|null
 */
function moveCharacter($direction)
{
    if (!isCharacterSelected()) {
        redirect('/');

        return null;
    }

    $locationModifiers = [
        'north' => [
            'x' => 0,
            'y' => -1
        ],
        'east' => [
            'x' => 1,
            'y' => 0
        ],
        'south' => [
            'x' => 0,
            'y' => +1
        ],
        'west' => [
            'x' => -1,
            'y' => 0
        ]
    ];


    $activeCharacter = getSelectedCharacter();
    $activeCharacter['inventory'] = getEquipmentForCharacter($activeCharacter['name']);
    $viewPort = config('viewport');
    $tileSize = config('tileSize');

    list($layers, $mapData) = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);
    if (!isset($layers['collision'])) {
        trigger_error('Map does not have a "collision" layer');
        return;
    }
    $collisionData = $layers['collision'];
    $locationModifier = $locationModifiers[$direction];
    $x = ~~($viewPort['width'] / 2) + $locationModifier['x'];
    $y = ~~($viewPort['height'] / 2) + $locationModifier['y'];
    $index = $viewPort['width'] * $y + $x;

    $isBlocked = (bool)$collisionData[$index] && isset($collisionData[$index]['tileSetName']);

    $newX = (int)$activeCharacter['x'];
    $newY = (int)$activeCharacter['y'];
    if (!$isBlocked) {
        $newX = $activeCharacter['x'] + $locationModifier['x'];
        $newY = $activeCharacter['y'] + $locationModifier['y'];
        updateCharacterLocation($newX, $newY, $activeCharacter['map'], $activeCharacter['name']);
    }
    $layerData = $layers['events'];
    $baseTile = $mapData['baseTile'];
    $absoluteX = $newX * $baseTile['width'];
    $absoluteY = $newY * $baseTile['height'];
    foreach ($layerData['objects'] as $object) {

        if ($absoluteX >= $object['x'] &&
            $absoluteX < $object['x'] + $object['width'] &&
            $absoluteY >= $object['y'] &&
            $absoluteY < $object['y'] + $object['height']
        ) {
            event($object['name'], $object['properties']);
        }

    }


    return router('/map/' . $direction);
}


/**
 * @param string $direction
 * @return mixed|null
 */
function ajaxMoveCharacter($direction)
{
    if (!isCharacterSelected()) {
        redirect('/');
        return null;
    }

    $locationModifiers = [
        'north' => [
            'x' => 0,
            'y' => -1
        ],
        'east' => [
            'x' => 1,
            'y' => 0
        ],
        'south' => [
            'x' => 0,
            'y' => +1
        ],
        'west' => [
            'x' => -1,
            'y' => 0
        ]
    ];


    $activeCharacter = getSelectedCharacter();
    $activeCharacter['inventory'] = getEquipmentForCharacter($activeCharacter['name']);
    $viewPort = config('viewport');
    $tileSize = config('tileSize');

    list($layers, $mapData) = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);
    if (!isset($layers['collision'])) {
        trigger_error('Map does not have a "collision" layer');
        return;
    }

    $collisionData = $layers['collision'];
    $locationModifier = $locationModifiers[$direction];
    $x = ~~($viewPort['width'] / 2) + $locationModifier['x'];
    $y = ~~($viewPort['height'] / 2) + $locationModifier['y'];
    $index = $viewPort['width'] * $y + $x;

    $isBlocked = (bool)$collisionData[$index] && isset($collisionData[$index]['tileSetName']);

    if ($isBlocked) {
        header('Content-Type:application/json;charset=utf8');
        echo json_encode([]);
        return;
    }
    $newX = $activeCharacter['x'] + $locationModifier['x'];
    $newY = $activeCharacter['y'] + $locationModifier['y'];
    updateCharacterLocation($newX, $newY, $activeCharacter['map'], $activeCharacter['name']);

    unset($layers['collision']);

    $layerData = $layers['events'];
    $baseTile = $mapData['baseTile'];
    $absoluteX = $newX * $baseTile['width'];
    $absoluteY = $newY * $baseTile['height'];
    foreach ($layerData['objects'] as $object) {

        if ($absoluteX >= $object['x'] &&
            $absoluteX < $object['x'] + $object['width'] &&
            $absoluteY >= $object['y'] &&
            $absoluteY < $object['y'] + $object['height']
        ) {
            event($object['name'], $object['properties']);
        }

    }
    unset($layers['events']);


    $activeCharacter = getSelectedCharacter();
    list($newLayers, $newMapData) = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);
    $newLayers = addCharacterToMap($newLayers, $viewPort['width'], $viewPort['height'], $activeCharacter);

    unset($newLayers['events']);
    unset($newLayers['collision']);

    header('Content-Type:application/json;charset=utf8');
    $response = [
        'layers' => $newLayers,
        'character' =>$activeCharacter
    ];
    echo json_encode($response);

}

/**
 * @param int $newX
 * @param int $newY
 * @param string $mapName
 * @param string $characterName
 */
function updateCharacterLocation($newX, $newY, $mapName, $characterName)
{
    $db = getDb();
    $mapName = mysqli_real_escape_string($db, $mapName);
    $characterName = mysqli_real_escape_string($db, $characterName);
    $sql = "UPDATE characters SET map = '" . $mapName . "',x=" . (int)$newX . ", y= " . (int)$newY . " WHERE name = '" . $characterName . "'";

    $result = mysqli_query($db, $sql);
    if (!$result) {
        trigger_error(mysqli_error($db));
    }
}

/**
 * @param null|string $character
 * @return null
 */
function askToDeleteCharacter($character = null)
{
    if (!isLoggedIn()) {
        return event('http.403');
    }
    session('characterName', null);
    list($characters, $activeCharacter) = initializeCharacterData($character);
    $data = [
        'characters' => $characters,
        'activeCharacter' => $activeCharacter,
        'equipmentSlots' => config('equipmentSlots'),
        'isDeletion' => true
    ];
    session('characterToDelete', $activeCharacter['name']);

    echo render('selectCharacter', $data);

    return null;
}

/**
 * @return null
 */
function deleteCharacter()
{
    if (!isLoggedIn()) {
        return event('http.403');
    }
    session('characterName', null);
    $characterName = session('characterToDelete');

    if (!$characterName) {
        router('/character/new');
        return null;
    }
    $sql = sprintf('DELETE FROM characters WHERE name = "%s" AND userId = %d',
        queryEscape($characterName),
        getCurrentUserId()
    );
    query($sql);
    session('characterToDelete', null);
    redirect('/character/new');

    return null;
}

/**
 * @param string $character
 * @return null
 */
function selectCharacter($character)
{
    if (!isLoggedIn()) {
        return event('http.403');
    }
    session('characterToDelete', null);
    session('characterName', null);
    if (getCharacterForUser($character, getCurrentUsername())) {
        session('characterName', $character);
    }
    redirect('/');

    return null;
}

/**
 * @param null|string $character
 * @return null
 */
function viewCharacter($character = null)
{
    if (!isLoggedIn()) {
        return event('http.403');
    }
    session('characterToDelete', null);
    session('characterName', null);
    list($characters, $activeCharacter) = initializeCharacterData($character);
    $data = [
        'characters' => $characters,
        'activeCharacter' => $activeCharacter,
        'equipmentSlots' => config('equipmentSlots'),
        'isDeletion' => false
    ];

    echo render('selectCharacter', $data);

    return null;
}

/**
 * @return null
 */
function newCharacter()
{
    if (!isLoggedIn()) {
        return event('http.403');
    }
    session('characterToDelete', null);
    session('characterName', null);
    navigation(_('Select character'), '/character/view');
    navigation(_('Logout'), '/logout');

    activateNavigation('/character/select');

    $characters = getCharactersForUser(getCurrentUsername());

    $characterName = '';
    $characterClass = '';
    $characterGender = '';
    $errors = [];

    if (isPost()) {
        $characterName = filter_input(INPUT_POST, 'characterName', FILTER_SANITIZE_STRING);
        $characterClass = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
        $characterGender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        $nameErrors = validateCharacterName($characterName);
        $classErrors = validateCharacterClass($characterClass);
        $genderErrors = validateCharacterGender($characterGender);
        $errors = array_merge($nameErrors, $classErrors, $genderErrors);
        if (count($errors) === 0) {
            if ($characterId = createCharacter(getCurrentUserId(), $characterName, $characterClass, $characterGender)) {
                $newCharacter = [
                    'id' => $characterId,
                    'name' => $characterName,
                    'class' => $characterClass,
                    'gender' => $characterGender
                ];
                event('game.newCharacter', $newCharacter);
                redirect('/character/view/' . $characterName);
            }
            $errors[] = _('Failed to create character');
        }
    }
    $newCharacter = [
        'name' => $characterName,
        'class' => $characterClass,
        'gender' => $characterGender
    ];
    $data = [
        'characters' => $characters,
        'newCharacter' => $newCharacter,
        'errors' => $errors
    ];

    echo render('newCharacter', $data);

    return null;
}

/**
 * @param int $userId
 * @param string $characterName
 * @param string $characterClass
 * @param string $characterGender
 * @return int|null|string
 */
function createCharacter($userId, $characterName, $characterClass, $characterGender)
{
    $sql = sprintf('INSERT INTO characters(name, userId, class, gender) VALUES ("%s", %d, "%s", %d)',
        queryEscape($characterName),
        $userId,
        queryEscape($characterClass),
        (int)($characterGender === 'male')
    );
    $result = query($sql);
    if (!$result) {
        trigger_error(getDbError());
        return null;
    }
    $db = getDb();

    return mysqli_insert_id($db);
}

/**
 * @param string $gender
 * @return array
 */
function validateCharacterGender($gender)
{
    $errors = [];
    $availableGenders = ['male', 'female'];
    if (!(bool)$gender) {
        $errors[] = _('Please select a gender');
        return $errors;
    }
    if (!in_array($gender, $availableGenders)) {
        $errors[] = _('Invalid gender selected');
    }

    return $errors;
}

/**
 * @param string $characterClass
 * @return array
 */
function validateCharacterClass($characterClass)
{
    $errors = [];
    $availableClasses = ['warrior', 'ranger', 'mage'];
    if (!(bool)$characterClass) {
        $errors[] = _('Please select a class');
        return $errors;
    }
    if (!in_array($characterClass, $availableClasses)) {
        $errors[] = _('Invalid class selected');
    }

    return $errors;
}

/**
 * @param string $characterName
 * @param bool $firstErrorOnly
 * @return array
 */
function validateCharacterName($characterName)
{
    $errors = [];
    $minLength = 3;
    $maxLength = 32;
    $blacklist = getBadWords();

    if (!(bool)$characterName) {
        $errors[] = _('Character name is empty');
        return $errors;
    }
    if (preg_match('~\W+~', $characterName)) {
        $errors[] = _('Character name contain non word characters');
    }
    if (mb_strlen($characterName) < $minLength) {
        $errors[] = sprintf(_('Character name is too short, %d characters are at least required'), $minLength);
    }
    if (mb_strlen($characterName) >= $maxLength) {
        $errors[] = sprintf(_('Character name is too long, maximum %d characters'), $maxLength);
    }
    if (in_array($characterName, $blacklist)) {
        $errors[] = _("Selected name is not allowed to use");
    }
    if (characterNameExists($characterName)) {
        $errors[] = sprintf(_("The character name %s already exists"), $characterName);
    }
    return $errors;
}

/**
 * @param string $characterName
 * @return bool
 */
function characterNameExists($characterName)
{
    $sql = sprintf('SELECT 1 FROM characters WHERE name = "%s"',
        queryEscape($characterName)
    );
    $result = query($sql);
    if (!$result) {
        return false;
    }

    return (bool)$result->num_rows;
}

/**
 * @return array|null
 */
function getSelectedCharacter()
{
    return getCharacterForUser(getSelectedCharacterName(), getCurrentUsername());
}

/**
 * @param string $characterName
 * @param string $username
 * @return array|null
 */
function getCharacterForUser($characterName, $username)
{
    $sql = sprintf('SELECT characterId,name, class, gender, map, x, y 
FROM characters INNER JOIN users ON characters.userId = users.userId 
WHERE username = "%s" AND name = "%s" LIMIT 1',
        queryEscape($username),
        queryEscape($characterName)
    );
    $result = query($sql);
    if (!$result) {

        return null;
    }
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $row['gender'] = (int)$row['gender'] === 1 ? 'male' : 'female';
    }

    return $row;
}

/**
 * @param string $username
 * @return array
 */
function getCharactersForUser($username)
{
    $characters = [];
    $sql = sprintf('SELECT name, class, gender
 FROM characters INNER JOIN users ON characters.userId = users.userId 
 WHERE username = "%s" ORDER BY characters.lastAction DESC',
        queryEscape($username)
    );
    $result = query($sql);
    if (!$result) {
        return $characters;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $row['gender'] = (int)$row['gender'] === 1 ? 'male' : 'female';
        $characters[md5($row['name'])] = $row;
    }
    return $characters;

}
