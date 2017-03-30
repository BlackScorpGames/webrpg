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

    $mapData = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);
    if (!isset($mapData['collision'])) {
        trigger_error('Map does not have a "collision" layer');
        return;
    }
    $collisionData = $mapData['collision'];
    $locationModifier = $locationModifiers[$direction];
    $x = ~~($viewPort['width'] / 2) + $locationModifier['x'];
    $y = ~~($viewPort['height'] / 2) + $locationModifier['y'];
    $index = $viewPort['width'] * $y + $x;

    $isBlocked = (bool)$collisionData[$index];
    if (!$isBlocked) {
        $newX = $activeCharacter['x'] + $locationModifier['x'];
        $newY = $activeCharacter['y'] + $locationModifier['y'];
        updateCharacterLocation($newX, $newY, $activeCharacter);
    }


    return router('/map/' . $direction);
}

/**
 * @param string $characterName
 * @return array
 */
function updateCharacterLocation($newX, $newY, $activeCharacter)
{
    $db = getDb();
    $sql = "UPDATE characters SET x=" . (int)$newX . ", y= " . (int)$newY . " WHERE characterId = " . (int)$activeCharacter['characterId'];
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
 * @param int    $userId
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
    }
    if (!in_array($gender, $availableGenders)) {
        $errors[] = _('Invalid gender selected');
    }

    return array_shift($errors);
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
    }
    if (!in_array($characterClass, $availableClasses)) {
        $errors[] = _('Invalid class selected');
    }

    return array_shift($errors);
}

/**
 * @param string $characterName
 * @param bool   $firstErrorOnly
 * @return array
 */
function validateCharacterName($characterName, $firstErrorOnly = true)
{
    $errors = [];
    $minLength = 3;
    $maxLength = 32;
    $validations = [
        [
            _('Character name is empty'),
            function ($characterName) {
                return !(bool)$characterName;
            },
        ],
        [
            _('Character name contain non word characters'),
            function ($characterName) {
                return preg_match('~\W+~', $characterName);
            },
        ],
        [
            sprintf(_('Character name is too short, %d characters are at least required'), $minLength),
            function ($characterName) use ($minLength) {
                return mb_strlen($characterName) < $minLength;
            },
        ],
        [
            sprintf(_('Character name is too long, maximum %d characters'), $maxLength),
            function ($characterName) use ($maxLength) {
                return mb_strlen($characterName) >= $maxLength;
            },
        ],
        [
            _('Selected name is not allowed to use'),
            function ($characterName) {
                $blacklist = getBadWords();

                return in_array($characterName, $blacklist);
            },
        ],
        [
            sprintf(_('The character name %s already exists'), $characterName),
            function ($characterName) {
                return characterNameExists($characterName);
            },
        ],
    ];
    foreach ($validations as $validate) {
        list($message, $validator) = $validate;
        $result = true;
        if ($validator instanceof Closure) {
            $result = $validator($characterName);
        }
        if (!$result) {
            $errors[] = $message;
        }
        if ($firstErrorOnly) {
            return array_shift($errors);
        }
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
 */function getSelectedCharacter()
{    return getCharacterForUser(getSelectedCharacterName(),getCurrentUsername());
}

/**
 * @param string $characterName
 * @param string $username
 * @return array|null
 */
function getCharacterForUser($characterName, $username)
{
    $sql = sprintf('SELECT characterId,name, class, gender, map, x, y FROM characters INNER JOIN users ON characters.userId = users.userId WHERE username = "%s" AND name = "%s" LIMIT 1',
        queryEscape($username),
        queryEscape($characterName)
    );
    $result = query($sql);
    if (!$result) {
        return null;
    }
    $row = $result->fetch_assoc();
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
    $sql = sprintf('SELECT name, class, gender FROM characters INNER JOIN users ON characters.userId = users.userId WHERE username = "%s" ORDER BY characters.lastAction DESC',
        queryEscape($username)
    );
    $result = query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['gender'] = (int)$row['gender'] === 1 ? 'male' : 'female';
            $characters[md5($row['name'])] = $row;
        }
    }

    return $characters;
}
