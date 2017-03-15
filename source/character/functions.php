<?php
function isCharacterSelected()
{
    return isset($_SESSION['characterName']);
}

function selectCharacter($character = null)
{

    navigation(_('Select character'), '/selectCharacter');
    navigation(_('Logout'), '/logout');

    activateNavigation('/selectCharacter');

    $characters = getCharactersForUser(getCurrentUsername());

    $data = [
        'characters' => $characters,
        'activeCharacter' => $characters[0]
    ];
    echo render('selectCharacter', $data);
}

function newCharacter()
{

    navigation(_('Select character'), '/selectCharacter');
    navigation(_('Logout'), '/logout');

    activateNavigation('/selectCharacter');

    $characters = getCharactersForUser(getCurrentUsername());


    $characterName = '';
    $characterClass = '';
    $errors = [];


    if (isPost()) {
        $characterName = filter_input(INPUT_POST, 'characterName', FILTER_SANITIZE_STRING);
        $characterClass = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
        $nameErrors = validateCharacterName($characterName);
        $classErrors = validateCharacterClass($characterClass);
        $errors = array_merge($nameErrors, $classErrors);
    }
    $newCharacter = [
        'name' => $characterName,
        'class' => $characterClass
    ];
    $data = [
        'characters' => $characters,
        'newCharacter' => $newCharacter,
        'errors'=>$errors
    ];

    echo render('newCharacter', $data);
}

function validateCharacterClass($characterClass)
{
    $errors = [];

    return $errors;
}

function validateCharacterName($characterName)
{
    $errors = [];
    $minLength = 3;
    $maxLength = 32;
    $blacklist = getBlackList();

    if (!(bool)$characterName) {
        $errors[] = _('Character name is empty');
    }

    if (mb_strlen($characterName) < $minLength) {
        $errors[] = _(sprintf('Character name is too short, %d characters are at least required', $minLength));
    }
    if (mb_strlen($characterName) >= $maxLength) {
        $errors[] = _(sprintf('Character name is too long, maximum %d characters', $maxLength));
    }
    if (in_array($characterName, $blacklist)) {
        $errors[] = _("Selected name is not allowed to use");
    }
    if(characterNameExists($characterName)){
        $errors[] = _(sprintf("The character name %s already exists",$characterName));
    }
    return $errors;
}

function characterNameExists($characterName)
{
    $db = getDb();
    $characterName = mysqli_real_escape_string($db, $characterName);
    $sql = "SELECT 1 FROM characters WHERE name = '" . $characterName . "'";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        return false;
    }
    return (bool)$result->num_rows;
}

function getCharactersForUser($username)
{
    $db = getDb();
    $username = mysqli_real_escape_string($db, $username);
    $sql = "SELECT name FROM characters 
      INNER JOIN users ON(characters.userId = users.userId) 
      WHERE username = '" . $username . "'";

    $characters = [];

    $result = mysqli_query($db, $sql);
    if (!$result) {
        return $characters;
    }

    while ($row = $result->fetch_assoc()) {
        $characters[] = $row;
    }
    return $characters;
}